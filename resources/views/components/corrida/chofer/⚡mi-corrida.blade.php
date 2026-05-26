<?php

use App\Models\Asiento;
use App\Models\BoletoCliente;
use App\Models\Corrida;
use Carbon\Carbon;
use Livewire\Component;

new class extends Component
{
    public ?array  $corridaInfo        = null;
    public ?int    $corridaId          = null;
    public array   $asientos           = [];
    public array   $asientosOrganizados = [];

    // Asiento seleccionado para ver info
    public ?int    $asientoViendo      = null;
    public ?array  $infoBoleto         = null;

    public function mount(): void
    {
        $this->cargarMiCorrida();
    }

    private function cargarMiCorrida(): void
    {
        $user = auth()->user();
        $ahora = now();

        // ✅ Próxima corrida del chofer autenticado (no finalizada ni cancelada)
        $corrida = Corrida::with(['ruta', 'urban', 'boletos.boletoCliente', 'boletos.cliente'])
            ->where('id_usuario', $user->id_usuario)
            ->whereNotIn('estado', ['Finalizada', 'Cancelada'])
            ->where(function ($q) use ($ahora) {
                // Corridas que aún no han terminado (llegada futura)
                // O que terminaron hace menos de 30 minutos (margen extra)
                $q->where('datetime_llegada', '>=', $ahora)
                    ->orWhere('datetime_llegada', '>=', $ahora->copy()->subMinutes(30));
            })
            ->orderBy('datetime_salida')
            ->first();

        if (!$corrida) return;

        $this->corridaId = $corrida->id_corrida;

        $asientosOcupadosMap = BoletoCliente::whereHas(
            'boleto',
            fn($q) =>
            $q->where('id_corrida', $corrida->id_corrida)
                ->whereIn('estado', ['activo', 'apartado'])
        )
            ->with(['boleto:id_boleto,estado,descuento,id_corrida', 'asiento:id_asiento,nombre'])
            ->with(['boleto.cliente'])
            ->get()
            ->keyBy('id_asiento');

        $todosAsientos = Asiento::where('id_urban', $corrida->urban?->id_urban)
            ->orderBy('nombre')
            ->get();

        $tarifa    = (float) ($corrida->ruta?->tarifa_clientes ?? 0);
        $organizados   = [];
        $asientosPlano = [];

        foreach ($todosAsientos as $a) {
            $numero = (int) filter_var($a->nombre, FILTER_SANITIZE_NUMBER_INT);

            if ($numero == 3) {
                $fila = 0;
                $lado = 'right';
            } elseif ($numero <= 15) {
                $fila           = (int) ceil($numero / 3);
                $posicionEnFila = ($numero - 1) % 3;
                $lado           = ($posicionEnFila < 2) ? 'left' : 'right';
            } else {
                $fila               = 6;
                $posicionUltimaFila = $numero - 16;
                $lado               = ($posicionUltimaFila < 2) ? 'left' : 'right';
            }

            $estadoFinal = 'libre';
            $pasajero    = null;

            if ($asientosOcupadosMap->has($a->id_asiento)) {
                $bc          = $asientosOcupadosMap->get($a->id_asiento);
                $estadoFinal = strtolower($bc->boleto->estado) === 'apartado' ? 'apartado' : 'ocupado';
                $cliente     = $bc->boleto->cliente;
                $pasajero    = $cliente
                    ? trim("{$cliente->nombre} {$cliente->apellido_paterno} {$cliente->apellido_materno}")
                    : 'Sin nombre';
            }

            $datosAsiento = [
                'id'       => $a->id_asiento,
                'nombre'   => $a->nombre,
                'estado'   => $estadoFinal,
                'pasajero' => $pasajero,
            ];

            if (!isset($organizados[$fila])) {
                $organizados[$fila] = ['left' => [], 'right' => []];
            }

            $organizados[$fila][$lado][] = $datosAsiento;
            $asientosPlano[]             = $datosAsiento;
        }

        ksort($organizados);
        $this->asientosOrganizados = $organizados;
        $this->asientos            = $asientosPlano;

        $total    = $corrida->urban?->numero_asientos ?? 0;
        $ocupados = collect($asientosPlano)->whereIn('estado', ['ocupado', 'apartado'])->count();

        $this->corridaInfo = [
            'hora_salida'  => $corrida->datetime_salida->format('g:i A'),
            'hora_llegada' => $corrida->datetime_llegada?->format('g:i A') ?? 'N/A',
            'fecha'        => $corrida->datetime_salida->locale('es')->isoFormat('dddd D [de] MMMM, YYYY'),
            'origen'       => $corrida->ruta?->origen ?? 'Sin origen',
            'destino'      => $corrida->ruta?->nombre ?? 'Sin destino',
            'urban'        => $corrida->urban?->codigo_urban ?? '—',
            'tarifa'       => number_format($tarifa, 2),
            'tarifa_raw'   => $tarifa,
            'total'        => $total,
            'ocupados'     => $ocupados,
            'libres'       => max(0, $total - $ocupados),
        ];
    }

    // ✅ Ver info de un asiento sin comprar
    public function verAsiento(int $id): void
    {
        if ($this->asientoViendo === $id) {
            $this->asientoViendo = null;
            $this->infoBoleto    = null;
            return;
        }

        $this->asientoViendo = $id;
        $asiento = collect($this->asientos)->firstWhere('id', $id);

        if (!$asiento || $asiento['estado'] === 'libre') {
            $this->infoBoleto = null;
            return;
        }

        $bc = BoletoCliente::where('id_asiento', $id)
            ->whereHas(
                'boleto',
                fn($q) =>
                $q->where('id_corrida', $this->corridaId)
                    ->whereIn('estado', ['activo', 'apartado'])
            )
            ->with(['boleto.cliente'])
            ->first();

        if (!$bc) return;

        $boleto  = $bc->boleto;
        $cliente = $boleto->cliente;
        $tarifa  = $this->corridaInfo['tarifa_raw'] ?? 0;
        $descuento = (float) ($boleto->descuento ?? 0);

        $this->infoBoleto = [
            'asiento'     => $asiento['nombre'],
            'estado'      => $boleto->estado,
            'pasajero'    => $cliente
                ? trim("{$cliente->nombre} {$cliente->apellido_paterno} {$cliente->apellido_materno}")
                : 'Sin nombre',
            'folio'       => $boleto->folio,
            'tarifa'      => number_format($tarifa, 2),
            'descuento'   => $descuento,
            'total'       => number_format(max(0, $tarifa - $descuento), 2),
            'tipo_pago'   => $boleto->tipo_de_pago,
            'destino'     => $this->corridaInfo['destino'],
        ];
    }

    public function with(): array
    {
        return [
            'libres'    => collect($this->asientos)->where('estado', 'libre')->count(),
            'ocupados'  => collect($this->asientos)->where('estado', 'ocupado')->count(),
            'apartados' => collect($this->asientos)->where('estado', 'apartado')->count(),
        ];
    }
};
?>

<div class="space-y-6 pb-10">

    @if(!$corridaInfo)
    {{-- Sin corrida --}}
    <div class="flex flex-col items-center justify-center py-24 text-center
                    bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-neutral-800 flex items-center justify-center mb-4">
            <flux:icon name="bus" class="size-8 text-gray-400" />
        </div>
        <h3 class="font-bold text-gray-700 dark:text-gray-300 text-lg">Sin corridas próximas</h3>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">No tienes corridas programadas actualmente.</p>
    </div>

    @else

    {{-- ── DIV 1: Info de la corrida ── --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40">
                    <flux:icon name="bus" class="size-4 text-blue-600 dark:text-blue-400" />
                </span>
                <div>
                    <h2 class="font-bold text-gray-800 dark:text-white text-base">Mi Próxima Corrida</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 capitalize">{{ $corridaInfo['fecha'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <flux:button
                    href="{{ route('servicios.bitacora.pdf', $corridaId) }}"
                    target="_blank"
                    variant="outline"
                    size="sm"
                    icon="document-text"
                >
                    Generar bitácora
                </flux:button>
                <flux:badge color="green">{{ $libres }} Libres</flux:badge>
                @if($apartados > 0)
                <flux:badge color="amber">{{ $apartados }} Apartados</flux:badge>
                @endif
                <flux:badge color="red">{{ $ocupados }} Ocupados</flux:badge>
            </div>
        </div>

        <div class="p-6">
            <div class="flex flex-wrap gap-6">
                {{-- Ruta origen → destino --}}
                <div class="flex items-center gap-4 flex-1 min-w-64">
                    <div class="flex flex-col items-center gap-1">
                        <span class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/40 border-2 border-blue-300 dark:border-blue-700">
                            <flux:icon name="map-pin-plus" class="size-4 text-blue-600 dark:text-blue-400" />
                        </span>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase">Origen</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-gray-800 dark:text-white text-sm">{{ $corridaInfo['origen'] }}</span>
                        <span class="text-xs text-blue-600 dark:text-blue-400 font-semibold">{{ $corridaInfo['hora_salida'] }}</span>
                    </div>

                    <div class="flex-1 flex items-center gap-1 min-w-12">
                        <div class="flex-1 h-px border-t-2 border-dashed border-gray-300 dark:border-neutral-600"></div>
                        <flux:icon name="bus" class="size-4 text-gray-400 shrink-0" />
                        <div class="flex-1 h-px border-t-2 border-dashed border-gray-300 dark:border-neutral-600"></div>
                    </div>

                    <div class="flex flex-col">
                        <span class="font-bold text-gray-800 dark:text-white text-sm">{{ $corridaInfo['destino'] }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ $corridaInfo['hora_llegada'] }}</span>
                    </div>
                    <div class="flex flex-col items-center gap-1">
                        <span class="flex items-center justify-center w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-900/40 border-2 border-emerald-300 dark:border-emerald-700">
                            <flux:icon name="map-pin-x" class="size-4 text-emerald-600 dark:text-emerald-400" />
                        </span>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase">Destino</span>
                    </div>
                </div>

                <div class="hidden lg:block w-px bg-gray-100 dark:bg-neutral-800 self-stretch"></div>

                <div class="flex flex-wrap gap-5">
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase">Unidad</span>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                            <span class="font-bold text-gray-800 dark:text-white text-sm">{{ $corridaInfo['urban'] }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase">Tarifa</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400 text-xl">${{ $corridaInfo['tarifa'] }}</span>
                    </div>
                </div>
            </div>

            @if($corridaInfo['total'] > 0)
            @php $pct = round(($corridaInfo['ocupados'] / $corridaInfo['total']) * 100); @endphp
            <div class="mt-5 pt-5 border-t border-gray-100 dark:border-neutral-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Ocupación</span>
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $pct }}%</span>
                </div>
                <div class="w-full h-2 bg-gray-100 dark:bg-neutral-800 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all
                        {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 60 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                        style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── DIV 2: Mapa (70%) + Info asiento (30%) ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6">

        {{-- Mapa de asientos (70%) --}}
        <div class="lg:col-span-7 bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40">
                        <flux:icon name="layout-grid" class="size-4 text-blue-600 dark:text-blue-400" />
                    </span>
                    <h3 class="font-bold text-gray-800 dark:text-white text-base">Distribución de Asientos</h3>
                </div>
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-neutral-800 px-2.5 py-1 rounded-full">
                    {{ $corridaInfo['urban'] }}
                </span>
            </div>

            {{-- Leyenda --}}
            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600 dark:text-gray-400 mb-5 pb-4 border-b border-gray-100 dark:border-neutral-800">
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-emerald-100 dark:bg-emerald-900/40 border-2 border-emerald-400 inline-block"></span>Libre
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-red-100 dark:bg-red-900/40 border-2 border-red-400 inline-block"></span>Ocupado
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-amber-100 dark:bg-amber-900/40 border-2 border-amber-400 inline-block"></span>Apartado
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-blue-600 border-2 border-blue-700 inline-block"></span>Viendo
                </span>
            </div>

            {{-- Van visual --}}
            <div class="flex justify-center">
                <div class="relative select-none" style="width:270px">
                    <div class="relative rounded-[2.5rem] border-[3px] border-gray-300 dark:border-neutral-600
                                bg-gradient-to-b from-slate-100 to-gray-50 dark:from-neutral-800 dark:to-neutral-900
                                px-5 pt-7 pb-10 shadow-lg">

                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-28 h-7 rounded-b-2xl
                                    bg-sky-100/80 dark:bg-sky-900/30 border-b-2 border-sky-200 dark:border-sky-800"></div>
                        <div class="absolute -left-4 top-10 w-4 h-7 bg-gray-300 dark:bg-neutral-600 rounded-l-lg border border-gray-400 shadow-sm"></div>
                        <div class="absolute -right-4 top-10 w-4 h-7 bg-gray-300 dark:bg-neutral-600 rounded-r-lg border border-gray-400 shadow-sm"></div>
                        <div class="absolute -right-1 top-1/4 w-1.5 h-16 bg-gray-400 dark:bg-neutral-500 rounded-r-lg"></div>

                        {{-- Conductor --}}
                        <div class="flex items-center justify-between mb-3 mt-1">
                            <div class="flex items-center gap-2">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-md">
                                    <flux:icon name="bus" class="size-6 text-white" />
                                </div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Conductor</span>
                            </div>

                            {{-- Asiento 3 (copiloto) --}}
                            @if(isset($asientosOrganizados[0]['right'][0]))
                            @php
                            $seat = $asientosOrganizados[0]['right'][0];
                            $sc = match(true) {
                            $asientoViendo === $seat['id'] => 'bg-blue-600 border-blue-700 text-white shadow-lg ring-2 ring-blue-300 dark:ring-blue-700 scale-110',
                            $seat['estado'] === 'ocupado' => 'bg-red-100 dark:bg-red-900/40 border-red-400 text-red-600 cursor-pointer hover:opacity-80',
                            $seat['estado'] === 'apartado' => 'bg-amber-100 dark:bg-amber-900/40 border-amber-400 text-amber-700 cursor-pointer hover:opacity-80',
                            default => 'bg-emerald-100 dark:bg-emerald-900/40 border-emerald-400 text-emerald-700 cursor-pointer hover:bg-emerald-200',
                            };
                            @endphp
                            <button wire:click="verAsiento({{ $seat['id'] }})"
                                class="w-11 h-11 rounded-xl text-[11px] font-bold border-2 transition-all duration-150 flex items-center justify-center {{ $sc }}">
                                {{ $seat['nombre'] }}
                            </button>
                            @endif
                        </div>

                        <div class="border-t-2 border-dashed border-gray-300 dark:border-neutral-600 mb-4 -mx-2"></div>

                        {{-- Filas --}}
                        <div class="space-y-3 relative z-10">
                            @foreach($asientosOrganizados as $fila => $lados)
                            @continue($fila == 0)
                            @php $esFilaTrasera = (count($lados['left']) + count($lados['right'])) >= 4; @endphp
                            <div class="flex items-center justify-center {{ $esFilaTrasera ? 'gap-1.5' : 'gap-4' }}">
                                <div class="flex gap-1.5 justify-end {{ $esFilaTrasera ? '' : 'min-w-[86px]' }}">
                                    @foreach($lados['left'] as $seat)
                                    @php
                                    $sc = match(true) {
                                    $asientoViendo === $seat['id'] => 'bg-blue-600 border-blue-700 text-white shadow-lg ring-2 ring-blue-300 dark:ring-blue-700 scale-110',
                                    $seat['estado'] === 'ocupado' => 'bg-red-100 dark:bg-red-900/40 border-red-400 text-red-600 cursor-pointer hover:opacity-80',
                                    $seat['estado'] === 'apartado' => 'bg-amber-100 dark:bg-amber-900/40 border-amber-400 text-amber-700 cursor-pointer hover:opacity-80',
                                    default => 'bg-emerald-100 dark:bg-emerald-900/40 border-emerald-400 text-emerald-700 cursor-pointer hover:bg-emerald-200',
                                    };
                                    @endphp
                                    <button wire:click="verAsiento({{ $seat['id'] }})"
                                        class="w-10 h-10 rounded-xl text-[11px] font-bold border-2 transition-all duration-150 flex items-center justify-center {{ $sc }}"
                                        title="{{ $seat['nombre'] }} — {{ $seat['pasajero'] ?? 'Libre' }}">
                                        {{ $seat['nombre'] }}
                                    </button>
                                    @endforeach
                                </div>

                                @if(!$esFilaTrasera)
                                <div class="w-4 flex justify-center">
                                    <div class="w-px h-8 border-l-2 border-dashed border-gray-300 dark:border-neutral-600"></div>
                                </div>
                                @endif

                                <div class="flex gap-1.5 justify-start {{ $esFilaTrasera ? '' : 'min-w-[40px]' }}">
                                    @foreach($lados['right'] as $seat)
                                    @php
                                    $sc = match(true) {
                                    $asientoViendo === $seat['id'] => 'bg-blue-600 border-blue-700 text-white shadow-lg ring-2 ring-blue-300 dark:ring-blue-700 scale-110',
                                    $seat['estado'] === 'ocupado' => 'bg-red-100 dark:bg-red-900/40 border-red-400 text-red-600 cursor-pointer hover:opacity-80',
                                    $seat['estado'] === 'apartado' => 'bg-amber-100 dark:bg-amber-900/40 border-amber-400 text-amber-700 cursor-pointer hover:opacity-80',
                                    default => 'bg-emerald-100 dark:bg-emerald-900/40 border-emerald-400 text-emerald-700 cursor-pointer hover:bg-emerald-200',
                                    };
                                    @endphp
                                    <button wire:click="verAsiento({{ $seat['id'] }})"
                                        class="w-10 h-10 rounded-xl text-[11px] font-bold border-2 transition-all duration-150 flex items-center justify-center {{ $sc }}"
                                        title="{{ $seat['nombre'] }} — {{ $seat['pasajero'] ?? 'Libre' }}">
                                        {{ $seat['nombre'] }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-28 h-5
                                    bg-gray-200/80 dark:bg-neutral-700/60 border-t border-gray-300 rounded-b-[2.5rem]"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info del asiento seleccionado (30%) --}}
        <div class="lg:col-span-3 bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex flex-col">

            <div class="flex items-center gap-3 px-6 py-4 border-b border-neutral-100 dark:border-neutral-800">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40">
                    <flux:icon name="user-round" class="size-4 text-blue-600 dark:text-blue-400" />
                </span>
                <h3 class="font-bold text-gray-800 dark:text-white text-base">Info del Asiento</h3>
            </div>

            <div class="flex-1 p-6">
                @if(!$asientoViendo)
                {{-- Estado inicial --}}
                <div class="flex flex-col items-center justify-center h-full py-10 text-center">
                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-neutral-800 flex items-center justify-center mb-3">
                        <flux:icon name="layout-grid" class="size-7 text-gray-400" />
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                        Toca un asiento para ver su información
                    </p>
                </div>

                @elseif(!$infoBoleto)
                {{-- Asiento libre --}}
                @php $asientoNombre = collect($asientos)->firstWhere('id', $asientoViendo)['nombre'] ?? '—'; @endphp
                <div class="flex flex-col items-center justify-center h-full py-10 text-center">
                    <div class="w-14 h-14 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-3">
                        <flux:icon name="square-plus" class="size-7 text-emerald-500" />
                    </div>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">Asiento {{ $asientoNombre }}</p>
                    <flux:badge color="green" class="mt-2">Disponible</flux:badge>
                    <p class="text-xs text-gray-400 mt-3">Este asiento no tiene pasajero asignado.</p>
                </div>

                @else
                {{-- Info del pasajero --}}
                <div class="space-y-4">

                    {{-- Badge estado --}}
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Asiento {{ $infoBoleto['asiento'] }}
                        </span>
                        <flux:badge color="{{ $infoBoleto['estado'] === 'apartado' ? 'amber' : 'blue' }}" size="sm">
                            {{ ucfirst($infoBoleto['estado']) }}
                        </flux:badge>
                    </div>

                    {{-- Pasajero --}}
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-neutral-800 rounded-xl">
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center flex-shrink-0">
                            <flux:icon name="user-round" class="size-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-gray-400 uppercase">Pasajero</p>
                            <p class="font-bold text-gray-800 dark:text-white text-sm leading-tight">
                                {{ $infoBoleto['pasajero'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Destino --}}
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-neutral-800 rounded-xl">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center flex-shrink-0">
                            <flux:icon name="map-pinned" class="size-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-gray-400 uppercase">Destino</p>
                            <p class="font-bold text-gray-800 dark:text-white text-sm">{{ $infoBoleto['destino'] }}</p>
                        </div>
                    </div>

                    {{-- Precio --}}
                    <div class="p-4 bg-gray-50 dark:bg-neutral-800 rounded-xl space-y-2 text-sm">
                        <div class="flex justify-between text-gray-500 dark:text-gray-400">
                            <span>Tarifa base</span>
                            <span class="font-medium">${{ $infoBoleto['tarifa'] }}</span>
                        </div>
                        @if($infoBoleto['descuento'] > 0)
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                            <span>Descuento</span>
                            <span class="font-medium">— ${{ number_format($infoBoleto['descuento'], 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold text-gray-800 dark:text-white border-t border-gray-200 dark:border-neutral-700 pt-2">
                            <span>Total pagado</span>
                            <span class="text-blue-600 dark:text-blue-400 text-base">${{ $infoBoleto['total'] }}</span>
                        </div>
                    </div>

                    {{-- Folio y tipo de pago --}}
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 bg-gray-50 dark:bg-neutral-800 rounded-lg">
                            <p class="text-gray-400 uppercase font-semibold mb-0.5">Folio</p>
                            <p class="font-bold text-gray-700 dark:text-gray-300">{{ $infoBoleto['folio'] }}</p>
                        </div>
                        <div class="p-2.5 bg-gray-50 dark:bg-neutral-800 rounded-lg">
                            <p class="text-gray-400 uppercase font-semibold mb-0.5">Pago</p>
                            <p class="font-bold text-gray-700 dark:text-gray-300">{{ $infoBoleto['tipo_pago'] }}</p>
                        </div>
                    </div>

                </div>
                @endif
            </div>
        </div>

    </div>
    @endif
</div>