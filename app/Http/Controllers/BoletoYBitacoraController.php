<?php

namespace App\Http\Controllers;

use App\Models\Asiento;
use App\Models\Boleto;
use App\Models\BoletoCliente;
use App\Models\BoletoPaquete;
use App\Models\Cliente;
use App\Models\Corrida;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BoletoYBitacoraController extends Controller
{
    /**
     * Generar un boleto de cliente (pasajero)
     */
    public function generarBoletoCliente(Request $request)
    {
        $request->validate([
            'id_corrida'       => 'required|exists:corrida,id_corrida',
            'id_asiento'       => 'required|exists:asiento,id_asiento',
            'nombre_completo'  => 'nullable|string|min:3',
            'nombre'           => 'nullable|string',
            'apellido_paterno' => 'nullable|string',
            'apellido_materno' => 'nullable|string',
            'peso_equipaje'    => 'nullable|numeric|min:0',
            'tipo_de_pago'     => 'nullable|string',
            'descuento'        => 'nullable|numeric|min:0',
        ]);

        $corrida = Corrida::with('ruta', 'urban')->find($request->id_corrida);
        if (!$corrida) {
            return response()->json(['success' => false, 'message' => 'Corrida no encontrada.'], 404);
        }

        // Validar que el asiento pertenece a la urban de la corrida
        $asiento = Asiento::where('id_asiento', $request->id_asiento)
            ->where('id_urban', $corrida->id_urban)
            ->first();
        if (!$asiento) {
            return response()->json(['success' => false, 'message' => 'El asiento no pertenece a la unidad asignada a esta corrida.'], 422);
        }

        // Validar que el asiento no esté ocupado
        $ocupado = BoletoCliente::whereHas('boleto', function ($query) use ($corrida) {
            $query->where('id_corrida', $corrida->id_corrida)
                ->whereIn('estado', ['activo', 'apartado', 'Pagado']);
        })->where('id_asiento', $request->id_asiento)->exists();

        if ($ocupado) {
            return response()->json(['success' => false, 'message' => 'El asiento ya se encuentra ocupado o apartado para esta corrida.'], 422);
        }

        // Resolver o registrar cliente
        $clienteId = null;
        if ($request->filled('nombre_completo')) {
            $partes = preg_split('/\s+/', trim($request->nombre_completo), 3);
            $cliente = Cliente::create([
                'nombre'           => $partes[0] ?? $request->nombre_completo,
                'apellido_paterno' => $partes[1] ?? '',
                'apellido_materno' => $partes[2] ?? '',
            ]);
            $clienteId = $cliente->id_cliente;
        } elseif ($request->filled('nombre')) {
            $cliente = Cliente::create([
                'nombre'           => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno ?? '',
                'apellido_materno' => $request->apellido_materno ?? '',
            ]);
            $clienteId = $cliente->id_cliente;
        } else {
            return response()->json(['success' => false, 'message' => 'Debe proporcionar el nombre del pasajero (nombre_completo o nombre).'], 422);
        }

        try {
            $boleto = DB::transaction(function () use ($request, $corrida, $clienteId) {
                $tarifa = (float) ($corrida->ruta->tarifa_clientes ?? 0);
                $descuento = (float) ($request->descuento ?? 0);
                $total = max(0, $tarifa - $descuento);

                $venta = Venta::create([
                    'id_cliente' => $clienteId,
                    'subtotal'   => (int) round($tarifa),
                    'descuento'  => (int) round($descuento),
                    'total'      => (int) round($total),
                    'fecha'      => today(),
                ]);

                $detalle = DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                ]);

                // Generar Folio
                $next = (Boleto::max('id_boleto') ?? 0) + 1;
                $folio = 'BOL-' . now()->format('Y') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);

                $boleto = Boleto::create([
                    'id_corrida'       => $corrida->id_corrida,
                    'id_cliente'       => $clienteId,
                    'id_detalle_venta' => $detalle->id_detalle_venta,
                    'folio'            => $folio,
                    'estado'           => 'activo',
                    'tipo_de_pago'     => $request->tipo_de_pago ?? 'Efectivo',
                    'descuento'        => $descuento,
                ]);

                BoletoCliente::create([
                    'id_boleto'     => $boleto->id_boleto,
                    'id_asiento'    => $request->id_asiento,
                    'peso_equipaje' => $request->peso_equipaje ?? 0,
                ]);

                return $boleto;
            });

            return response()->json([
                'success' => true,
                'message' => 'Boleto de cliente generado correctamente.',
                'data' => [
                    'id_boleto'     => $boleto->id_boleto,
                    'folio'         => $boleto->folio,
                    'estado'        => $boleto->estado,
                    'tipo_de_pago'  => $boleto->tipo_de_pago,
                    'descuento'     => $boleto->descuento,
                    'cliente'       => $boleto->cliente,
                    'asiento'       => $asiento->nombre,
                    'peso_equipaje' => $request->peso_equipaje ?? 0,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el boleto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar un boleto de paquete
     */
    public function generarBoletoPaquete(Request $request)
    {
        $request->validate([
            'id_corrida'       => 'required|exists:corrida,id_corrida',
            'descripcion'      => 'required|string|max:255',
            'peso'             => 'required|numeric|min:0.1',
            'destinatario'     => 'required|string|max:255',
            'nombre_remitente' => 'nullable|string|min:3',
            'id_remitente'     => 'nullable|exists:cliente,id_cliente',
            'guia'             => 'nullable|string|unique:boleto_paquete,guia',
            'tipo_de_pago'     => 'nullable|string',
            'descuento'        => 'nullable|numeric|min:0',
        ]);

        $corrida = Corrida::with('ruta')->find($request->id_corrida);
        if (!$corrida) {
            return response()->json(['success' => false, 'message' => 'Corrida no encontrada.'], 404);
        }

        // Resolver remitente (cliente)
        $clienteId = null;
        if ($request->filled('id_remitente')) {
            $clienteId = $request->id_remitente;
        } elseif ($request->filled('nombre_remitente')) {
            $partes = preg_split('/\s+/', trim($request->nombre_remitente), 3);
            $cliente = Cliente::create([
                'nombre'           => $partes[0] ?? $request->nombre_remitente,
                'apellido_paterno' => $partes[1] ?? '',
                'apellido_materno' => $partes[2] ?? '',
            ]);
            $clienteId = $cliente->id_cliente;
        } else {
            return response()->json(['success' => false, 'message' => 'Debe proporcionar un remitente (nombre_remitente o id_remitente).'], 422);
        }

        try {
            $boleto = DB::transaction(function () use ($request, $corrida, $clienteId) {
                // Calcular tarifa del paquete
                $tarifaBase = ($corrida->ruta->tarifa_paquete > 0) ? (float)$corrida->ruta->tarifa_paquete : (float)($corrida->ruta->tarifa_clientes ?? 0);
                $peso = (float)$request->peso;
                $extra = ($peso > 5) ? ($peso - 5) * 10 : 0;
                $subtotal = $tarifaBase + $extra;
                $descuento = (float)($request->descuento ?? 0);
                $total = max(0, $subtotal - $descuento);

                $venta = Venta::create([
                    'id_cliente' => $clienteId,
                    'subtotal'   => (int) round($subtotal),
                    'descuento'  => (int) round($descuento),
                    'total'      => (int) round($total),
                    'fecha'      => today(),
                ]);

                $detalle = DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                ]);

                // Generar Folio
                $next = (Boleto::max('id_boleto') ?? 0) + 1;
                $folio = 'BOL-' . now()->format('Y') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);

                $boleto = Boleto::create([
                    'id_corrida'       => $corrida->id_corrida,
                    'id_cliente'       => $clienteId,
                    'id_detalle_venta' => $detalle->id_detalle_venta,
                    'folio'            => $folio,
                    'estado'           => 'activo',
                    'tipo_de_pago'     => $request->tipo_de_pago ?? 'Efectivo',
                    'descuento'        => $descuento,
                ]);

                // Generar Guia si no viene en el request
                $guia = $request->guia;
                if (empty($guia)) {
                    do {
                        $guia = 'GUIA-' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
                    } while (BoletoPaquete::where('guia', $guia)->exists());
                }

                BoletoPaquete::create([
                    'id_boleto'    => $boleto->id_boleto,
                    'guia'         => $guia,
                    'descripcion'  => $request->descripcion,
                    'peso'         => $peso,
                    'destinatario' => $request->destinatario,
                ]);

                return $boleto;
            });

            $boleto->load('boletoPaquete', 'cliente');

            return response()->json([
                'success' => true,
                'message' => 'Boleto de paquete generado correctamente.',
                'data' => [
                    'id_boleto'    => $boleto->id_boleto,
                    'folio'        => $boleto->folio,
                    'estado'       => $boleto->estado,
                    'tipo_de_pago' => $boleto->tipo_de_pago,
                    'descuento'    => $boleto->descuento,
                    'remitente'    => $boleto->cliente,
                    'paquete' => [
                        'guia'         => $boleto->boletoPaquete->guia,
                        'descripcion'  => $boleto->boletoPaquete->descripcion,
                        'peso'         => $boleto->boletoPaquete->peso,
                        'destinatario' => $boleto->boletoPaquete->destinatario,
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el paquete: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener la bitácora de viaje del chofer
     */
    public function obtenerBitacora(Request $request, $id_corrida)
    {
        $corrida = Corrida::with([
            'ruta.sucursalSalida',
            'ruta.sucursalLlegada',
            'urban',
            'user',
            'boletos' => function ($query) {
                $query->whereIn('estado', ['activo', 'apartado', 'Pagado']);
            },
            'boletos.cliente',
            'boletos.boletoCliente.asiento',
            'boletos.boletoPaquete',
            'boletos.detalleVenta.venta'
        ])->find($id_corrida);

        if (!$corrida) {
            if ($request->wantsJson() || $request->has('json')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Corrida no encontrada.'
                ], 404);
            }
            abort(404, 'Corrida no encontrada');
        }

        $pasajeros = [];
        $paqueteria = [];
        $totalEfectivoBoletos = 0;
        $totalEfectivoPaquetes = 0;

        foreach ($corrida->boletos as $boleto) {
            $ventaTotal = 0;
            if ($boleto->detalleVenta && $boleto->detalleVenta->venta) {
                $ventaTotal = (float)$boleto->detalleVenta->venta->total;
            } else {
                $tarifa = (float)($corrida->ruta->tarifa_clientes ?? 0);
                $ventaTotal = max(0, $tarifa - (float)$boleto->descuento);
            }

            $esEfectivo = strtolower(trim($boleto->tipo_de_pago)) === 'efectivo';

            if ($boleto->boletoCliente) {
                $cliente = $boleto->cliente;
                $nombreCompleto = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido_paterno ?? '') . ' ' . ($cliente->apellido_materno ?? ''));
                
                $pasajeros[] = [
                    'id_boleto'       => $boleto->id_boleto,
                    'folio'           => $boleto->folio,
                    'nombre_completo' => $nombreCompleto ?: 'Pasajero Desconocido',
                    'asiento'         => $boleto->boletoCliente->asiento->nombre ?? 'N/A',
                    'peso_equipaje'   => (float)($boleto->boletoCliente->peso_equipaje ?? 0),
                    'tipo_de_pago'    => $boleto->tipo_de_pago,
                    'descuento'       => (float)$boleto->descuento,
                    'total_pagado'    => $ventaTotal,
                ];

                if ($esEfectivo) {
                    $totalEfectivoBoletos += $ventaTotal;
                }
            }

            if ($boleto->boletoPaquete) {
                $remitente = $boleto->cliente;
                $nombreRemitente = trim(($remitente->nombre ?? '') . ' ' . ($remitente->apellido_paterno ?? '') . ' ' . ($remitente->apellido_materno ?? ''));

                $paqueteria[] = [
                    'id_boleto'    => $boleto->id_boleto,
                    'folio'        => $boleto->folio,
                    'guia'         => $boleto->boletoPaquete->guia,
                    'descripcion'  => $boleto->boletoPaquete->descripcion,
                    'peso'         => (float)($boleto->boletoPaquete->peso ?? 0),
                    'destinatario' => $boleto->boletoPaquete->destinatario,
                    'remitente'    => $nombreRemitente ?: 'Anónimo',
                    'tipo_de_pago' => $boleto->tipo_de_pago,
                    'descuento'    => (float)$boleto->descuento,
                    'total_pagado' => $ventaTotal,
                ];

                if ($esEfectivo) {
                    $totalEfectivoPaquetes += $ventaTotal;
                }
            }
        }

        $totalEfectivoGeneral = $totalEfectivoBoletos + $totalEfectivoPaquetes;

        $choferName = 'Sin asignar';
        if ($corrida->user) {
            $choferName = trim(($corrida->user->name ?? '') . ' ' . ($corrida->user->apellido_paterno ?? '') . ' ' . ($corrida->user->apellido_materno ?? ''));
        }

        $bitacora = [
            'corrida' => [
                'id_corrida'    => $corrida->id_corrida,
                'fecha_salida'  => $corrida->datetime_salida ? $corrida->datetime_salida->format('Y-m-d') : null,
                'hora_salida'   => $corrida->datetime_salida ? $corrida->datetime_salida->format('g:i A') : 'N/A',
                'fecha_llegada' => $corrida->datetime_llegada ? $corrida->datetime_llegada->format('Y-m-d') : null,
                'hora_llegada'  => $corrida->datetime_llegada ? $corrida->datetime_llegada->format('g:i A') : 'N/A',
                'ruta'          => $corrida->ruta->nombre ?? 'Sin ruta',
                'origen'        => $corrida->ruta->sucursalSalida->nombre ?? 'N/A',
                'destino'       => $corrida->ruta->sucursalLlegada->nombre ?? 'N/A',
                'unidad_urban'  => $corrida->urban->codigo_urban ?? 'N/A',
                'chofer'        => $choferName,
                'estado'        => $corrida->estado ?? 'Pendiente',
            ],
            'pasajeros'  => $pasajeros,
            'paqueteria' => $paqueteria,
            'resumen_financiero' => [
                'total_efectivo_boletos'    => $totalEfectivoBoletos,
                'total_efectivo_paqueteria' => $totalEfectivoPaquetes,
                'total_efectivo_general'    => $totalEfectivoGeneral,
            ]
        ];

        if ($request->wantsJson() || $request->has('json')) {
            return response()->json([
                'success' => true,
                'data' => $bitacora
            ]);
        }

        return view('bitacora.show', compact('bitacora'));
    }

    /**
     * Descargar el PDF de un boleto de cliente
     */
    public function descargarBoletoClientePDF($id_boleto)
    {
        $boleto = Boleto::with([
            'cliente',
            'boletoCliente.asiento',
            'corrida.ruta.sucursalSalida',
            'corrida.ruta.sucursalLlegada',
            'corrida.urban',
            'corrida.user'
        ])->find($id_boleto);

        if (!$boleto || !$boleto->boletoCliente) {
            abort(404, 'Boleto de cliente no encontrado');
        }

        $pdf = Pdf::loadView('pdf.boleto_cliente_pdf', compact('boleto'));
        
        return $pdf->stream('boleto_cliente_' . $boleto->folio . '.pdf');
    }

    /**
     * Descargar el PDF de un boleto de paquete
     */
    public function descargarBoletoPaquetePDF($id_boleto)
    {
        $boleto = Boleto::with([
            'cliente',
            'boletoPaquete',
            'corrida.ruta.sucursalSalida',
            'corrida.ruta.sucursalLlegada',
            'corrida.urban'
        ])->find($id_boleto);

        if (!$boleto || !$boleto->boletoPaquete) {
            abort(404, 'Boleto de paquete no encontrado');
        }

        $pdf = Pdf::loadView('pdf.boleto_paquete_pdf', compact('boleto'));

        return $pdf->stream('boleto_paquete_' . $boleto->boletoPaquete->guia . '.pdf');
    }

    /**
     * Descargar el PDF de la bitácora de viaje de la corrida
     */
    public function descargarBitacoraPDF($id_corrida)
    {
        $corrida = Corrida::with([
            'ruta.sucursalSalida',
            'ruta.sucursalLlegada',
            'urban',
            'user',
            'boletos' => function ($query) {
                $query->whereIn('estado', ['activo', 'apartado', 'Pagado']);
            },
            'boletos.cliente',
            'boletos.boletoCliente.asiento',
            'boletos.boletoPaquete',
            'boletos.detalleVenta.venta'
        ])->find($id_corrida);

        if (!$corrida) {
            abort(404, 'Corrida no encontrada');
        }

        $pasajeros = [];
        $paqueteria = [];
        $totalEfectivoBoletos = 0;
        $totalEfectivoPaquetes = 0;

        foreach ($corrida->boletos as $boleto) {
            $ventaTotal = 0;
            if ($boleto->detalleVenta && $boleto->detalleVenta->venta) {
                $ventaTotal = (float)$boleto->detalleVenta->venta->total;
            } else {
                $tarifa = (float)($corrida->ruta->tarifa_clientes ?? 0);
                $ventaTotal = max(0, $tarifa - (float)$boleto->descuento);
            }

            $esEfectivo = strtolower(trim($boleto->tipo_de_pago)) === 'efectivo';

            if ($boleto->boletoCliente) {
                $cliente = $boleto->cliente;
                $nombreCompleto = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido_paterno ?? '') . ' ' . ($cliente->apellido_materno ?? ''));
                
                $pasajeros[] = [
                    'id_boleto'       => $boleto->id_boleto,
                    'folio'           => $boleto->folio,
                    'nombre_completo' => $nombreCompleto ?: 'Pasajero Desconocido',
                    'asiento'         => $boleto->boletoCliente->asiento->nombre ?? 'N/A',
                    'peso_equipaje'   => (float)($boleto->boletoCliente->peso_equipaje ?? 0),
                    'tipo_de_pago'    => $boleto->tipo_de_pago,
                    'descuento'       => (float)$boleto->descuento,
                    'total_pagado'    => $ventaTotal,
                ];

                if ($esEfectivo) {
                    $totalEfectivoBoletos += $ventaTotal;
                }
            }

            if ($boleto->boletoPaquete) {
                $remitente = $boleto->cliente;
                $nombreRemitente = trim(($remitente->nombre ?? '') . ' ' . ($remitente->apellido_paterno ?? '') . ' ' . ($remitente->apellido_materno ?? ''));

                $paqueteria[] = [
                    'id_boleto'    => $boleto->id_boleto,
                    'folio'        => $boleto->folio,
                    'guia'         => $boleto->boletoPaquete->guia,
                    'descripcion'  => $boleto->boletoPaquete->descripcion,
                    'peso'         => (float)($boleto->boletoPaquete->peso ?? 0),
                    'destinatario' => $boleto->boletoPaquete->destinatario,
                    'remitente'    => $nombreRemitente ?: 'Anónimo',
                    'tipo_de_pago' => $boleto->tipo_de_pago,
                    'descuento'    => (float)$boleto->descuento,
                    'total_pagado' => $ventaTotal,
                ];

                if ($esEfectivo) {
                    $totalEfectivoPaquetes += $ventaTotal;
                }
            }
        }

        $totalEfectivoGeneral = $totalEfectivoBoletos + $totalEfectivoPaquetes;

        $choferName = 'Sin asignar';
        if ($corrida->user) {
            $choferName = trim(($corrida->user->name ?? '') . ' ' . ($corrida->user->apellido_paterno ?? '') . ' ' . ($corrida->user->apellido_materno ?? ''));
        }

        $bitacora = [
            'corrida' => [
                'id_corrida'    => $corrida->id_corrida,
                'fecha_salida'  => $corrida->datetime_salida ? $corrida->datetime_salida->format('Y-m-d') : null,
                'hora_salida'   => $corrida->datetime_salida ? $corrida->datetime_salida->format('g:i A') : 'N/A',
                'fecha_llegada' => $corrida->datetime_llegada ? $corrida->datetime_llegada->format('Y-m-d') : null,
                'hora_llegada'  => $corrida->datetime_llegada ? $corrida->datetime_llegada->format('g:i A') : 'N/A',
                'ruta'          => $corrida->ruta->nombre ?? 'Sin ruta',
                'origen'        => $corrida->ruta->sucursalSalida->nombre ?? 'N/A',
                'destino'       => $corrida->ruta->sucursalLlegada->nombre ?? 'N/A',
                'unidad_urban'  => $corrida->urban->codigo_urban ?? 'N/A',
                'chofer'        => $choferName,
                'estado'        => $corrida->estado ?? 'Pendiente',
            ],
            'pasajeros'  => $pasajeros,
            'paqueteria' => $paqueteria,
            'resumen_financiero' => [
                'total_efectivo_boletos'    => $totalEfectivoBoletos,
                'total_efectivo_paqueteria' => $totalEfectivoPaquetes,
                'total_efectivo_general'    => $totalEfectivoGeneral,
            ]
        ];

        $pdf = Pdf::loadView('pdf.bitacora_pdf', compact('bitacora'));

        return $pdf->stream('bitacora_corrida_' . $id_corrida . '.pdf');
    }
}
