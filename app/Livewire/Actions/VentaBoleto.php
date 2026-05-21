<?php

namespace App\Livewire\Actions;

use App\Models\Asiento;
use App\Models\Boleto;
use App\Models\BoletoCliente;
use App\Models\Cliente;
use App\Models\Corrida;
use App\Models\DetalleVenta;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class VentaBoleto extends Component
{
    public string $filtroFecha = '';
    public string $filtroRuta = '';

    // ── Corrida seleccionada ──────────────────────────────────
    public ?int $corridaId = null;
    public ?array $corridaData = null;

    // ── Asientos ─────────────────────────────────────────────
    public array $asientos = [];
    public array $asientosOrganizados = [];
    public ?int $cantidadBoletos = null;
    public array $asientosSeleccionados = [];
    public array $boletosSeleccionados = [];

    // ── Búsqueda cliente ─────────────────────────────────────
    public string $busquedaCliente = '';
    public array $clientesResultados = [];
    public ?int $clienteId = null;
    public bool $mostrarResultados = false;

    // ── Datos de la venta / boletos ───────────────────────────
    public string $nombreCompleto = '';
    public string $pesoEquipaje = '';
    public string $tipoPago = 'Efectivo';
    public float $descuento = 0;
    public string $categoriaDescuento = '';
    public string $abordarEn = '';
    public string $bajarEn = '';
    public string $folio = '';

    // ── UI ────────────────────────────────────────────────────
    public string $flashMsg = '';
    public string $flashType = 'success';

    public function mount(): void
    {
        $this->folio = $this->generarFolio();
        $this->filtroFecha = today()->format('Y-m-d');
    }

    // ── Hooks de ciclo de vida ────────────────────────────────

    // ELIMINADO: updatedFiltroFecha() — filtroFecha ya no vive aquí,
    // lo maneja TablaCorridasDia internamente.

    public function updatedCantidadBoletos(): void
    {
        $cantidad = (int) ($this->cantidadBoletos ?: 0);

        if ($cantidad <= 0) {
            $this->limpiarBoletosSeleccionados();
            return;
        }

        if (count($this->asientosSeleccionados) > $cantidad) {
            $seleccionRecortada = array_slice($this->asientosSeleccionados, 0, $cantidad);
            $this->asientosSeleccionados = $seleccionRecortada;
            $this->boletosSeleccionados = array_intersect_key($this->boletosSeleccionados, array_flip($seleccionRecortada));
        }
    }

    public function updatedBusquedaCliente(): void
    {
        if ($this->clienteId) {
            $this->clienteId = null;
            $this->nombreCompleto = '';
        }

        $texto = trim($this->busquedaCliente);

        if (strlen($texto) < 2) {
            $this->clientesResultados = [];
            $this->mostrarResultados = false;
            return;
        }

        $this->clientesResultados = Cliente::where('nombre', 'like', "%$texto%")
            ->orWhere('apellido_paterno', 'like', "%$texto%")
            ->orWhere('apellido_materno', 'like', "%$texto%")
            ->limit(6)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id_cliente,
                'nombre' => trim("{$c->nombre} {$c->apellido_paterno} {$c->apellido_materno}"),
            ])
            ->toArray();

        $this->mostrarResultados = count($this->clientesResultados) > 0;
    }

    public function updatedCategoriaDescuento(): void
    {
        $base = (float) ($this->corridaData['tarifa_raw'] ?? 0);

        $this->descuento = match ($this->categoriaDescuento) {
            'estudiante' => round($base * 0.10, 2),
            'adulto_mayor' => round($base * 0.20, 2),
            'nino' => round($base * 0.15, 2),
            default => 0.00,
        };
    }

    // ── Listeners del componente hijo TablaCorridasDia ────────

    #[On('corrida-seleccionada')]
    public function seleccionarCorrida(int $id): void
    {
        if ($this->corridaId === $id) {
            $this->resetCorrida();

            return;
        }

        $corrida = Corrida::with(['ruta', 'urban', 'boletos', 'user'])->find($id);
        if (!$corrida) {
            return;
        }

        $this->corridaId = $id;
        $this->corridaData = $this->mapearCorrida($corrida);
        $this->cantidadBoletos = null;
        $this->limpiarBoletosSeleccionados();

        $this->cargarAsientos($corrida);
        $this->dispatch('scroll-to-asientos');
    }

    #[On('corrida-deseleccionada')]
    public function manejarDeseleccion(): void
    {
        $this->resetCorrida();
    }

    // ── Acciones ─────────────────────────────────────────────

    public function seleccionarAsiento(int $id): void
    {
        if (!$this->cantidadBoletos) {
            return;
        }

        $asiento = collect($this->asientos)->firstWhere('id', $id);
        if (!$asiento || $asiento['estado'] !== 'libre') {
            return;
        }

        if (in_array($id, $this->asientosSeleccionados, true)) {
            $this->asientosSeleccionados = array_values(array_filter(
                $this->asientosSeleccionados,
                fn ($asientoId) => $asientoId !== $id,
            ));

            unset($this->boletosSeleccionados[$id]);
            return;
        }

        if (count($this->asientosSeleccionados) >= (int) $this->cantidadBoletos) {
            return;
        }

        $this->asientosSeleccionados[] = $id;
        $this->boletosSeleccionados[$id] = [
            'id_asiento' => $id,
            'nombre_asiento' => $asiento['nombre'],
            'nombreCompleto' => '',
            'pesoEquipaje' => '',
            'descuento' => 0,
        ];
    }

    public function seleccionarCliente(int $id): void
    {
        $match = collect($this->clientesResultados)->firstWhere('id', $id);
        if (!$match) {
            return;
        }

        $this->clienteId = $id;
        $this->nombreCompleto = $match['nombre'];
        $this->busquedaCliente = $match['nombre'];
        $this->mostrarResultados = false;
        $this->clientesResultados = [];
    }

    public function limpiarCliente(): void
    {
        $this->clienteId = null;
        $this->busquedaCliente = '';
        $this->nombreCompleto = '';
        $this->mostrarResultados = false;
        $this->clientesResultados = [];
    }

    public function cerrarResultados(): void
    {
        $this->mostrarResultados = false;
    }

    public function confirmarVenta(): void
    {
        $this->procesarVenta(false);
    }

    public function apartar(): void
    {
        $this->procesarVenta(true);
    }

    public function cancelar(): void
    {
        $this->resetFormulario();
        $this->resetCorrida();
    }

    // ── Helpers privados ──────────────────────────────────────

    private function procesarVenta(bool $esApartado): void
    {
        $seleccionados = $this->obtenerBoletosSeleccionadosOrdenados();

        $this->validate($this->reglasValidacionBoletos($seleccionados), $this->mensajesValidacionBoletos());

        try {
            if (!$this->corridaId || !$this->corridaData) {
                throw new \Exception('Selecciona una corrida.');
            }

            if ((int) $this->cantidadBoletos !== count($this->asientosSeleccionados)) {
                throw new \Exception('Selecciona exactamente la cantidad de boletos indicada.');
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (!$user->hasRole('cajero')) {
                throw new \Exception('Acceso denegado: Solo los cajeros pueden realizar ventas.');
            }

            $turnoActivo = $user->turnoActivo;
            if (!$turnoActivo) {
                throw new \Exception('No se encontró un turno abierto.');
            }

            DB::transaction(function () use ($esApartado, $seleccionados) {
                $this->asegurarAsientosDisponibles($this->asientosSeleccionados);

                $clienteVentaId = $this->resolverClienteDesdeNombre($this->nombreCompleto);
                $tarifa = (float) ($this->corridaData['tarifa_raw'] ?? 0);
                $subtotal = count($seleccionados) * $tarifa;
                $descuentoTotal = 0;

                foreach ($seleccionados as $ticket) {
                    $descuentoTotal += $esApartado
                        ? 0
                        : min($tarifa, max(0, (float) ($ticket['descuento'] ?? 0)));
                }

                $total = $esApartado ? 0 : max(0, $subtotal - $descuentoTotal);

                $venta = Venta::create([
                    'id_cliente' => $clienteVentaId,
                    'subtotal' => (int) round($subtotal),
                    'descuento' => (int) round($esApartado ? 0 : $descuentoTotal),
                    'total' => (int) round($total),
                    'fecha' => today(),
                ]);

                $detalle = DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                ]);

                foreach ($seleccionados as $ticket) {
                    $clientePasajeroId = $this->resolverClienteDesdeNombre($ticket['nombreCompleto']);
                    $descuentoTicket = $esApartado
                        ? 0
                        : min($tarifa, max(0, (float) ($ticket['descuento'] ?? 0)));

                    $boleto = Boleto::create([
                        'id_corrida' => $this->corridaId,
                        'id_cliente' => $clientePasajeroId,
                        'id_detalle_venta' => $detalle->id_detalle_venta,
                        'folio' => $this->generarFolio(),
                        'estado' => $esApartado ? 'apartado' : 'activo',
                        'tipo_de_pago' => $esApartado ? 'Pendiente' : $this->tipoPago,
                        'descuento' => $descuentoTicket,
                    ]);

                    BoletoCliente::create([
                        'id_boleto' => $boleto->id_boleto,
                        'id_asiento' => $ticket['id_asiento'],
                        'peso_equipaje' => max(0, (float) ($ticket['pesoEquipaje'] ?? 0)),
                    ]);
                }
            });

            $cantidad = count($seleccionados);
            $this->flashMsg = $esApartado
                ? "✓ {$cantidad} boletos apartados correctamente."
                : "✓ {$cantidad} boletos registrados correctamente.";
            $this->flashType = 'success';
            $this->resetFormulario();
            $this->recargarAsientos();
        } catch (\Throwable $e) {
            $this->flashMsg = $esApartado
                ? "Error al apartar: {$e->getMessage()}"
                : "Error al registrar los boletos: {$e->getMessage()}";
            $this->flashType = 'error';
        }
    }

    private function reglasValidacionBoletos(array $seleccionados): array
    {
        $reglas = [
            'corridaId' => 'required',
            'cantidadBoletos' => 'required|integer|min:1',
            'nombreCompleto' => 'required|min:3',
            'tipoPago' => 'required',
            'asientosSeleccionados' => 'required|array',
            'boletosSeleccionados' => 'required|array',
        ];

        foreach ($seleccionados as $ticket) {
            $idAsiento = $ticket['id_asiento'];
            $reglas["boletosSeleccionados.$idAsiento.nombreCompleto"] = 'required|min:3';
            $reglas["boletosSeleccionados.$idAsiento.pesoEquipaje"] = 'nullable|numeric|min:0';
            $reglas["boletosSeleccionados.$idAsiento.descuento"] = 'nullable|numeric|min:0';
        }

        return $reglas;
    }

    private function mensajesValidacionBoletos(): array
    {
        return [
            'corridaId.required' => 'Selecciona una corrida.',
            'cantidadBoletos.required' => 'Selecciona la cantidad de boletos.',
            'cantidadBoletos.min' => 'Selecciona al menos un boleto.',
            'nombreCompleto.required' => 'Ingresa el nombre del cliente que realizará la compra.',
            'nombreCompleto.min' => 'El nombre debe tener al menos 3 caracteres.',
            'tipoPago.required' => 'Selecciona un tipo de pago.',
            'asientosSeleccionados.required' => 'Selecciona al menos un asiento.',
            'boletosSeleccionados.required' => 'Completa los datos de cada boleto.',
        ];
    }

    private function limpiarBoletosSeleccionados(): void
    {
        $this->asientosSeleccionados = [];
        $this->boletosSeleccionados = [];
    }

    private function cargarAsientos(Corrida $corrida): void
    {
        if (! $corrida->urban) {
            $this->asientosOrganizados = [];
            $this->asientos = [];
            return;
        }

        $asientosOcupadosMap = BoletoCliente::whereHas('boleto', function ($query) use ($corrida) {
            $query->where('id_corrida', $corrida->id_corrida)
                ->whereIn('estado', ['activo', 'apartado']);
        })
            ->with('boleto:id_boleto,estado')
            ->get()
            ->keyBy('id_asiento');

        $todosAsientos = Asiento::where('id_urban', $corrida->urban->id_urban)
            ->orderBy('nombre')
            ->get();

        $organizados = [];
        $asientosPlano = [];

        foreach ($todosAsientos as $a) {
            $numero = (int) filter_var($a->nombre, FILTER_SANITIZE_NUMBER_INT);

            if ($numero == 3) {
                $fila = 0;
                $lado = 'right';
            } elseif ($numero <= 15) {
                $fila = (int) ceil($numero / 3);
                $posicionEnFila = ($numero - 1) % 3;
                $lado = ($posicionEnFila < 2) ? 'left' : 'right';
            } else {
                $fila = 6;
                $posicionUltimaFila = $numero - 16;
                $lado = ($posicionUltimaFila < 2) ? 'left' : 'right';
            }

            $estadoFinal = 'libre';
            if ($asientosOcupadosMap->has($a->id_asiento)) {
                $estadoFinal = match (strtolower($asientosOcupadosMap->get($a->id_asiento)->boleto->estado)) {
                    'activo' => 'ocupado',
                    'apartado' => 'apartado',
                    default => 'libre',
                };
            }

            $datosAsiento = [
                'id' => $a->id_asiento,
                'nombre' => $a->nombre,
                'estado' => $estadoFinal,
            ];

            if (! isset($organizados[$fila])) {
                $organizados[$fila] = ['left' => [], 'right' => []];
            }

            $organizados[$fila][$lado][] = $datosAsiento;
            $asientosPlano[] = $datosAsiento;
        }

        ksort($organizados);
        $this->asientosOrganizados = $organizados;
        $this->asientos = $asientosPlano;
    }

    private function recargarAsientos(): void
    {
        if (!$this->corridaId) {
            return;
        }

        $corrida = Corrida::with('urban')->find($this->corridaId);
        if ($corrida) {
            $this->cargarAsientos($corrida);
        }
    }

    private function asegurarAsientosDisponibles(array $asientoIds): void
    {
        $asientosOcupados = BoletoCliente::whereIn('id_asiento', $asientoIds)
            ->whereHas('boleto', function ($query) {
                $query->where('id_corrida', $this->corridaId)
                    ->whereIn('estado', ['activo', 'apartado']);
            })
            ->pluck('id_asiento')
            ->all();

        if (!$asientosOcupados) {
            return;
        }

        $nombres = collect($this->asientos)
            ->whereIn('id', $asientosOcupados)
            ->pluck('nombre')
            ->implode(', ');

        throw new \Exception('Los asientos ' . ($nombres ?: implode(', ', $asientosOcupados)) . ' ya no están disponibles.');
    }

    private function obtenerBoletosSeleccionadosOrdenados(): array
    {
        return collect($this->asientosSeleccionados)
            ->map(fn ($asientoId) => $this->boletosSeleccionados[$asientoId] ?? null)
            ->filter()
            ->values()
            ->toArray();
    }

    private function resolverClienteDesdeNombre(string $nombreCompleto): int
    {
        $nombreCompleto = trim(preg_replace('/\s+/', ' ', $nombreCompleto) ?? $nombreCompleto);
        $partes = preg_split('/\s+/', $nombreCompleto, 3) ?: [];
        $partes = array_pad($partes, 3, '');

        $cliente = Cliente::query()
            ->where('nombre', $partes[0])
            ->where('apellido_paterno', $partes[1])
            ->where('apellido_materno', $partes[2])
            ->first();

        if ($cliente) {
            return $cliente->id_cliente;
        }

        $cliente = Cliente::create([
            'nombre' => $partes[0] !== '' ? $partes[0] : $nombreCompleto,
            'apellido_paterno' => $partes[1] ?? '',
            'apellido_materno' => $partes[2] ?? '',
        ]);

        return $cliente->id_cliente;
    }

    private function mapearCorrida(Corrida $corrida): array
    {
        $total = $corrida->urban?->numero_asientos ?? 0;
        $vendidos = $corrida->boletos->count();
        $libres = max(0, $total - $vendidos);

        // Usar datetime_salida / datetime_llegada (columnas reales de la BD)
        $salida = $corrida->datetime_salida
            ? Carbon::parse($corrida->datetime_salida)
            : null;

        $llegada = $corrida->datetime_llegada
            ? Carbon::parse($corrida->datetime_llegada)
            : null;

        $estado = 'Pendiente';
        if ($salida && $salida->isPast()) {
            $estado = ($llegada && $llegada->isPast()) ? 'Finalizado' : 'En Camino';
        }

        return [
            'id' => $corrida->id_corrida,
            'hora_salida' => $salida ? $salida->format('g:i A') : 'N/A',
            'hora_llegada' => $llegada ? $llegada->format('g:i A') : 'N/A',
            'ruta' => $corrida->ruta?->nombre ?? 'Sin ruta',
            'tarifa' => number_format($corrida->ruta?->tarifa_clientes ?? 0, 2),
            'tarifa_raw' => (float) ($corrida->ruta?->tarifa_clientes ?? 0),
            'codigo_urban' => $corrida->urban?->codigo_urban ?? '—',
            'chofer' => $corrida->user?->name ?? 'Sin asignar',
            'libres' => $libres,
            'total' => $total,
            'lleno' => $total > 0 && $libres === 0,
            'estado' => $estado,
        ];
    }

    private function resetCorrida(): void
    {
        $this->corridaId = null;
        $this->corridaData = null;
        $this->asientos = [];
        $this->asientosOrganizados = [];
        $this->cantidadBoletos = null;
        $this->limpiarBoletosSeleccionados();
    }

    private function resetFormulario(): void
    {
        $this->clienteId = null;
        $this->busquedaCliente = '';
        $this->clientesResultados = [];
        $this->mostrarResultados = false;
        $this->nombreCompleto = '';
        $this->pesoEquipaje = '';
        $this->tipoPago = 'Efectivo';
        $this->descuento = 0;
        $this->categoriaDescuento = '';
        $this->abordarEn = '';
        $this->bajarEn = '';
        $this->cantidadBoletos = null;
        $this->limpiarBoletosSeleccionados();
        $this->folio = $this->generarFolio();
    }

    private function generarFolio(): string
    {
        $next = (Boleto::max('id_boleto') ?? 0) + 1;

        return 'BOL-'.now()->format('Y').'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        $boletosSeleccionadosOrdenados = $this->obtenerBoletosSeleccionadosOrdenados();
        $tarifaBase = $this->corridaData ? (float) ($this->corridaData['tarifa_raw'] ?? 0) : 0;
        $subtotalVenta = count($boletosSeleccionadosOrdenados) * $tarifaBase;

        $descuentoTotal = collect($boletosSeleccionadosOrdenados)
            ->sum(fn ($ticket) => min($tarifaBase, max(0, (float) ($ticket['descuento'] ?? 0))));

        $totalAPagar = $this->corridaData
            ? max(0, $subtotalVenta - $descuentoTotal)
            : 0;

        return view('livewire.venta-boleto', [
            'totalAPagar' => $totalAPagar,
            'subtotalVenta' => $subtotalVenta,
            'descuentoTotal' => $descuentoTotal,
            'tarifaBase' => $tarifaBase,
            'boletosSeleccionadosOrdenados' => $boletosSeleccionadosOrdenados,
        ]);
    }
}
