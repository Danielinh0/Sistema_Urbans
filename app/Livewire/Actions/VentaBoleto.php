<?php

namespace App\Livewire\Actions;

use App\Models\Asiento;
use App\Models\Boleto;
use App\Models\BoletoCliente;
use App\Models\Cliente;
use App\Models\Corrida;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class VentaBoleto extends Component
{
    // ── Filtros ───────────────────────────────────────────────
    public string $filtroFecha = '';
    public string $filtroRuta  = '';

    // ── Corrida seleccionada ──────────────────────────────────
    public ?int   $corridaId   = null;
    public ?array $corridaData = null;

    // ── Asientos ─────────────────────────────────────────────
    public array  $asientos      = [];
    public ?int   $asientoId     = null;
    public string $asientoNombre = '';
    public array $asientosOrganizados = [];

    // ── Búsqueda cliente ─────────────────────────────────────
    public string $busquedaCliente    = '';
    public array  $clientesResultados = [];
    public ?int   $clienteId          = null;
    public bool   $mostrarResultados  = false;

    // ── Datos del boleto ──────────────────────────────────────
    public string $nombreCompleto     = '';
    public string $pesoEquipaje       = '';
    public string $tipoPago           = 'Efectivo';
    public float  $descuento          = 0;
    public string $categoriaDescuento = '';
    public string $abordarEn          = '';
    public string $bajarEn            = '';
    public string $folio              = '';

    // ── UI ────────────────────────────────────────────────────
    public string $flashMsg  = '';
    public string $flashType = 'success';

    // ─────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->filtroFecha = today()->format('Y-m-d');
        $this->folio       = $this->generarFolio();
    }

    // ── Hooks de ciclo de vida ────────────────────────────────

    public function updatedFiltroFecha(): void
    {
        $this->resetCorrida();
    }

    public function updatedBusquedaCliente(): void
    {
        // Si el usuario edita el campo después de haber elegido un cliente, lo limpiamos
        if ($this->clienteId) {
            $this->clienteId      = null;
            $this->nombreCompleto = '';
        }

        $texto = trim($this->busquedaCliente);

        if (strlen($texto) < 2) {
            $this->clientesResultados = [];
            $this->mostrarResultados  = false;
            return;
        }

        $this->clientesResultados = Cliente::where('nombre', 'like', "%$texto%")
            ->orWhere('apellido_paterno', 'like', "%$texto%")
            ->orWhere('apellido_materno', 'like', "%$texto%")
            ->limit(6)
            ->get()
            ->map(fn($c) => [
                'id'     => $c->id_cliente,
                'nombre' => trim("{$c->nombre} {$c->apellido_paterno} {$c->apellido_materno}"),
            ])
            ->toArray();

        $this->mostrarResultados = count($this->clientesResultados) > 0;
    }

    public function updatedCategoriaDescuento(): void
    {
        $base = (float) ($this->corridaData['tarifa_raw'] ?? 0);

        $this->descuento = match ($this->categoriaDescuento) {
            'estudiante'   => round($base * 0.10, 2),
            'adulto_mayor' => round($base * 0.20, 2),
            'nino'         => round($base * 0.15, 2),
            default        => 0.00,
        };
    }

    // ── Acciones ─────────────────────────────────────────────

    public function seleccionarCorrida(int $id): void
    {
        if ($this->corridaId === $id) {
            $this->resetCorrida();
            return;
        }

        $corrida = Corrida::with(['ruta', 'urban', 'boletos', 'user'])->find($id);
        if (!$corrida) return;

        $this->corridaId   = $id;
        $this->corridaData = $this->mapearCorrida($corrida);
        $this->asientoId   = null;
        $this->asientoNombre = '';

        $this->cargarAsientos($corrida);
    }

    public function seleccionarAsiento(int $id): void
    {
        $asiento = collect($this->asientos)->firstWhere('id', $id);
        if (!$asiento || $asiento['estado'] !== 'libre') return;

        if ($this->asientoId === $id) {
            $this->asientoId     = null;
            $this->asientoNombre = '';
        } else {
            $this->asientoId     = $id;
            $this->asientoNombre = $asiento['nombre'];
        }
    }

    public function seleccionarCliente(int $id): void
    {
        $match = collect($this->clientesResultados)->firstWhere('id', $id);
        if (!$match) return;

        $this->clienteId          = $id;
        $this->nombreCompleto     = $match['nombre'];
        $this->busquedaCliente    = $match['nombre'];
        $this->mostrarResultados  = false;
        $this->clientesResultados = [];
    }

    public function limpiarCliente(): void
    {
        $this->clienteId          = null;
        $this->busquedaCliente    = '';
        $this->nombreCompleto     = '';
        $this->mostrarResultados  = false;
        $this->clientesResultados = [];
    }

    public function cerrarResultados(): void
    {
        $this->mostrarResultados = false;
    }

    public function confirmarVenta(): void
    {
        $this->validate([
            'corridaId'      => 'required',
            'asientoId'      => 'required',
            'nombreCompleto' => 'required|min:3',
            'tipoPago'       => 'required',
        ], [
            'corridaId.required'      => 'Selecciona una corrida.',
            'asientoId.required'      => 'Selecciona un asiento.',
            'nombreCompleto.required' => 'Ingresa el nombre del pasajero.',
            'nombreCompleto.min'      => 'El nombre debe tener al menos 3 caracteres.',
        ]);

        try {

            $idUsuarioLogueado = Auth::id();

            $turnoActivo = \App\Models\Turno::where('id_usuario', $idUsuarioLogueado)
                ->latest('id_turno')
                ->first();

            if (!$turnoActivo) {
                throw new \Exception("No tienes un turno abierto.");
            }
            $clienteId = $this->resolverCliente();

            $boleto = Boleto::create([
                'id_corrida'   => $this->corridaId,
                'id_cliente'   => $clienteId,
                'id_turno'     => $turnoActivo->id_turno,
                'folio'        => $this->folio,
                'estado'       => 'activo',
                'tipo_de_pago' => $this->tipoPago,
                'descuento'    => $this->descuento,
            ]);

            BoletoCliente::create([
                'id_boleto'     => $boleto->id_boleto,
                'id_asiento'    => $this->asientoId,
                'peso_equipaje' => $this->pesoEquipaje ?: 0,
            ]);

            $this->flashMsg  = "✓ Boleto {$this->folio} registrado correctamente.";
            $this->flashType = 'success';
            $this->resetFormulario();
            $this->recargarAsientos();
        } catch (\Throwable $e) {
            $this->flashMsg  = "Error al registrar el boleto: {$e->getMessage()}";
            $this->flashType = 'error';
        }
    }

    public function apartar(): void
    {
        $this->validate([
            'corridaId'      => 'required',
            'asientoId'      => 'required',
            'nombreCompleto' => 'required|min:3',
        ]);

        try {
            $idUsuarioLogueado = Auth::id();

            $turnoActivo = \App\Models\Turno::where('id_usuario', $idUsuarioLogueado)
                ->latest('id_turno')
                ->first();

            if (!$turnoActivo) {
                throw new \Exception("No tienes un turno abierto.");
            }
            $clienteId = $this->resolverCliente();

            $boleto = Boleto::create([
                'id_corrida'   => $this->corridaId,
                'id_cliente'   => $clienteId,
                'id_turno'     => $turnoActivo->id_turno,
                'folio'        => $this->folio,
                'estado'       => 'apartado',
                'tipo_de_pago' => $this->tipoPago ?: 'Pendiente',
                'descuento'    => $this->descuento,
            ]);

            BoletoCliente::create([
                'id_boleto'     => $boleto->id_boleto,
                'id_asiento'    => $this->asientoId,
                'peso_equipaje' => $this->pesoEquipaje ?: 0,
            ]);

            $this->flashMsg  = "⏳ Asiento apartado — Folio: {$this->folio}";
            $this->flashType = 'success';
            $this->resetFormulario();
            $this->recargarAsientos();
        } catch (\Throwable $e) {
            $this->flashMsg  = "Error al apartar: {$e->getMessage()}";
            $this->flashType = 'error';
        }
    }

    public function cancelar(): void
    {
        $this->resetFormulario();
        $this->resetCorrida();
    }

    // ── Helpers privados ──────────────────────────────────────

    private function cargarAsientos(Corrida $corrida): void
    {
        if (!$corrida->urban) {
            $this->asientosOrganizados = [];
            $this->asientos = [];
            return;
        }

        // 1. Obtenemos los asientos ocupados para ESTA corrida específica.
        // Relacionamos BoletoCliente -> Boleto para filtrar por id_corrida y estado.
        $asientosOcupadosMap = BoletoCliente::whereHas('boleto', function ($query) use ($corrida) {
            $query->where('id_corrida', $corrida->id_corrida)
                ->whereIn('estado', ['activo', 'apartado']);
        })
            ->with('boleto:id_boleto,estado') // Cargamos el estado del boleto
            ->get()
            ->keyBy('id_asiento'); // Usamos el id_asiento como llave para búsqueda rápida

        // 2. Traemos la lista maestra de asientos de la unidad (Urban)
        $todosAsientos = Asiento::where('id_urban', $corrida->urban->id_urban)
            ->orderBy('nombre')
            ->get();

        $organizados = [];
        $asientosPlano = [];

        foreach ($todosAsientos as $a) {
            // --- Lógica de Posicionamiento (A01, A02, etc.) ---
            $numero = (int) substr($a->nombre, 1);
            if ($numero <= 12) {
                $fila = (string) ceil($numero / 3);
                $posicion = ($numero - 1) % 3 + 1;
                $lado = ($posicion <= 2) ? 'left' : 'right';
            } else {
                $fila = '5';
                $posicion = $numero - 12;
                $lado = ($posicion <= 4) ? 'left' : 'right';
            }

            // --- Determinación del Estado ---
            $estadoFinal = 'libre';

            // Verificamos si este ID de asiento existe en nuestro mapa de boletos de la corrida
            if ($asientosOcupadosMap->has($a->id_asiento)) {
                $boletoRelacionado = $asientosOcupadosMap->get($a->id_asiento)->boleto;

                // Mapeamos el estado del boleto al estado del asiento para la UI
                $estadoFinal = match (strtolower($boletoRelacionado->estado)) {
                    'activo'   => 'ocupado',
                    'apartado' => 'apartado',
                    default    => 'libre',
                };
            }

            $datosAsiento = [
                'id'     => $a->id_asiento,
                'nombre' => $a->nombre,
                'estado' => $estadoFinal,
            ];

            $asientosPlano[] = $datosAsiento;
            $organizados[$fila][$lado][] = $datosAsiento;
        }

        ksort($organizados, SORT_NUMERIC);

        $this->asientosOrganizados = $organizados;
        $this->asientos = $asientosPlano;
    }

    private function recargarAsientos(): void
    {
        if (!$this->corridaId) return;
        $corrida = Corrida::with('urban')->find($this->corridaId);
        if ($corrida) $this->cargarAsientos($corrida);
    }

    private function resolverCliente(): int
    {
        if ($this->clienteId) return $this->clienteId;

        $partes  = preg_split('/\s+/', trim($this->nombreCompleto), 3);
        $cliente = Cliente::create([
            'nombre'           => $partes[0] ?? $this->nombreCompleto,
            'apellido_paterno' => $partes[1] ?? '',
            'apellido_materno' => $partes[2] ?? '',
        ]);

        return $cliente->id_cliente;
    }

    private function mapearCorrida(Corrida $corrida): array
    {
        $total    = $corrida->urban?->numero_asientos ?? 0;
        $vendidos = $corrida->boletos->count();
        $libres   = max(0, $total - $vendidos);

        $estado = 'Pendiente';
        if ($corrida->hora_salida) {
            try {
                $salida = Carbon::parse($corrida->fecha)->setTimeFromTimeString($corrida->hora_salida);

                $llegada = $corrida->hora_llegada
                    ? Carbon::parse($corrida->fecha)->setTimeFromTimeString($corrida->hora_llegada)
                    : null;

                if ($salida->isPast()) {
                    $estado = ($llegada && $llegada->isPast()) ? 'Finalizado' : 'En Camino';
                }
            } catch (\Exception $e) {
                $estado = 'Error de Formato';
            }
        }

        return [
            'id'           => $corrida->id_corrida,
            'hora_salida'  => $corrida->hora_salida
                ? Carbon::parse($corrida->hora_salida)->format('g:i A')
                : 'N/A',
            'hora_llegada' => $corrida->hora_llegada
                ? Carbon::parse($corrida->hora_llegada)->format('g:i A')
                : 'N/A',
            'ruta'         => $corrida->ruta?->nombre ?? 'Sin ruta',
            'tarifa'       => number_format($corrida->ruta?->tarifa_clientes ?? 0, 2),
            'tarifa_raw'   => (float) ($corrida->ruta?->tarifa_clientes ?? 0),
            'codigo_urban' => $corrida->urban?->codigo_urban ?? '—',
            'chofer'       => $corrida->user?->name ?? 'Sin asignar',
            'libres'       => $libres,
            'total'        => $total,
            'lleno'        => $total > 0 && $libres === 0,
            'estado'       => $estado,
        ];
    }

    private function resetCorrida(): void
    {
        $this->corridaId     = null;
        $this->corridaData   = null;
        $this->asientos      = [];
        $this->asientoId     = null;
        $this->asientoNombre = '';
    }

    private function resetFormulario(): void
    {
        $this->asientoId          = null;
        $this->asientoNombre      = '';
        $this->clienteId          = null;
        $this->busquedaCliente    = '';
        $this->clientesResultados = [];
        $this->mostrarResultados  = false;
        $this->nombreCompleto     = '';
        $this->pesoEquipaje       = '';
        $this->tipoPago           = 'Efectivo';
        $this->descuento          = 0;
        $this->categoriaDescuento = '';
        $this->abordarEn          = '';
        $this->bajarEn            = '';
        $this->folio              = $this->generarFolio();
    }

    private function generarFolio(): string
    {
        $next = (Boleto::max('id_boleto') ?? 0) + 1;
        return 'BOL-' . now()->format('Y') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        $corridas = Corrida::with(['ruta', 'urban', 'boletos', 'user'])
            ->whereDate('fecha', $this->filtroFecha ?: today())
            ->when(
                $this->filtroRuta,
                fn($q) =>
                $q->whereHas(
                    'ruta',
                    fn($r) =>
                    $r->where('nombre', 'like', "%{$this->filtroRuta}%")
                )
            )
            ->orderBy('hora_salida', 'asc')
            ->get()
            ->map(fn($c) => $this->mapearCorrida($c))
            ->toArray();

        $totalAPagar = max(0, ($this->corridaData['tarifa_raw'] ?? 0) - $this->descuento);

        return view('livewire.venta-boleto', [
            'corridas' => $corridas,
            'asientosOrganizados' => $this->asientosOrganizados, // Pasamos la propiedad directamente
            'totalAPagar' => $totalAPagar,
        ]);
    }
}
