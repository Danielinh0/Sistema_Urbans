<?php

use App\Models\Asiento;
use App\Models\BoletoCliente;
use App\Models\Corrida;
use Carbon\Carbon;
use Livewire\Component;

new class extends Component {

    // ── Props ─────────────────────────────────────────────────
    public int $corridaId;

    // ── Estado interno ────────────────────────────────────────
    public ?array $corridaInfo  = null;
    public array  $asientos     = [];
    public string $flashMsg     = '';
    public string $flashType    = 'success';

    // ─────────────────────────────────────────────────────────

    public function mount(int $corridaId): void
    {
        $this->corridaId = $corridaId;
        $this->cargarCorrida();
    }

    private function cargarCorrida(): void
    {
        $corrida = Corrida::with([
            'ruta',
            'urban',
            'user',
            'boletos.boletoCliente.asiento',
            'boletos.boletoCliente.cliente',
        ])->find($this->corridaId);

        if (!$corrida) {
            $this->flashMsg  = 'No se encontró la corrida solicitada.';
            $this->flashType = 'error';
            return;
        }

        $asientosOcupadosMap = BoletoCliente::whereHas('boleto', function ($q) {
            $q->where('id_corrida', $this->corridaId)
                ->whereIn('estado', ['activo', 'apartado']);
        })
            ->with([
                'boleto:id_boleto,estado,descuento,id_corrida',
                'asiento:id_asiento,nombre',
            ])
            ->with(['boleto.cliente'])
            ->get()
            ->keyBy('id_asiento');

        $todosAsientos = Asiento::where('id_urban', $corrida->urban?->id_urban)
            ->orderBy('nombre')
            ->get();

        $tarifa = (float) ($corrida->ruta?->tarifa_clientes ?? 0);

        $this->asientos = $todosAsientos->map(function ($a) use ($asientosOcupadosMap, $tarifa) {
            if ($asientosOcupadosMap->has($a->id_asiento)) {
                $bc     = $asientosOcupadosMap->get($a->id_asiento);
                $boleto = $bc->boleto;

                $cliente = $boleto->cliente ?? null;
                $nombre  = $cliente
                    ? trim("{$cliente->nombre} {$cliente->apellido_paterno} {$cliente->apellido_materno}")
                    : ($boleto->nombre_pasajero ?? 'Pasajero sin nombre');

                $descuento  = (float) ($boleto->descuento ?? 0);
                $costoFinal = max(0, $tarifa - $descuento);

                return [
                    'id'      => $a->id_asiento,
                    'nombre'  => $a->nombre,
                    'estado'  => strtolower($boleto->estado) === 'apartado' ? 'apartado' : 'ocupado',
                    'pasajero' => $nombre,
                    'costo'   => number_format($costoFinal, 2),
                ];
            }

            return [
                'id'      => $a->id_asiento,
                'nombre'  => $a->nombre,
                'estado'  => 'libre',
                'pasajero' => null,
                'costo'   => null,
            ];
        })->toArray();

        $total    = $corrida->urban?->numero_asientos ?? 0;
        $ocupados = collect($this->asientos)->whereIn('estado', ['ocupado', 'apartado'])->count();
        $libres   = max(0, $total - $ocupados);

        $this->corridaInfo = [
            'hora_salida'  => $corrida->hora_salida
                ? Carbon::parse($corrida->hora_salida)->format('g:i A')
                : 'N/A',
            'hora_llegada' => $corrida->hora_llegada
                ? Carbon::parse($corrida->hora_llegada)->format('g:i A')
                : 'N/A',
            'fecha'        => Carbon::parse($corrida->fecha)
                ->locale('es')
                ->isoFormat('dddd D [de] MMMM, YYYY'),
            'origen'       => $corrida->ruta?->origen        ?? 'Sin origen',
            'destino'      => $corrida->ruta?->nombre        ?? 'Sin destino',
            'urban'        => $corrida->urban?->codigo_urban  ?? '—',
            'conductor'    => $corrida->user?->name           ?? 'Sin asignar',
            'tarifa'       => number_format($tarifa, 2),
            'total'        => $total,
            'ocupados'     => $ocupados,
            'libres'       => $libres,
        ];
    }

    public function with(): array
    {
        $libres   = collect($this->asientos)->where('estado', 'libre')->count();
        $ocupados = collect($this->asientos)->where('estado', 'ocupado')->count();
        $apartados = collect($this->asientos)->where('estado', 'apartado')->count();

        return compact('libres', 'ocupados', 'apartados');
    }
};
?>

<div class="space-y-6 pb-10">

    {{-- ── Flash ── --}}
    @if($flashMsg)
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium
        {{ $flashType === 'success'
            ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800'
            : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800' }}"
        wire:key="flash">
        @if($flashType === 'success')
        <flux:icon.check-circle class="size-5 shrink-0" />
        @else
        <flux:icon.minus-circle class="size-5 shrink-0" />
        @endif
        {{ $flashMsg }}
        <button wire:click="$set('flashMsg', '')" class="ml-auto opacity-60 hover:opacity-100">✕</button>
    </div>
    @endif

    @if($corridaInfo)

    {{-- DIV 1 — Info General --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40">
                    {{-- Nota: Heroicons no tiene 'bus', usamos 'truck' como alternativa --}}
                    <flux:icon.truck class="size-4 text-blue-600 dark:text-blue-400" />
                </span>
                <div>
                    <h2 class="font-bold text-gray-800 dark:text-white text-base">Detalle de Corrida</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 capitalize">{{ $corridaInfo['fecha'] }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <flux:badge color="green">{{ $libres }} Libres</flux:badge>
                @if($apartados > 0)
                <flux:badge color="amber">{{ $apartados }} Apartados</flux:badge>
                @endif
                <flux:badge color="red">{{ $ocupados }} Ocupados</flux:badge>
            </div>
        </div>

        <div class="p-6">
            <div class="flex flex-wrap gap-6">
                {{-- Ruta --}}
                <div class="flex items-center gap-4 flex-1 min-w-64">
                    <div class="flex flex-col items-center gap-1">
                        <span class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/40 border-2 border-blue-300 dark:border-blue-700">
                            <flux:icon.map-pin class="size-4 text-blue-600 dark:text-blue-400" />
                        </span>
                        <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase">Origen</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-gray-800 dark:text-white text-sm leading-tight">{{ $corridaInfo['origen'] }}</span>
                        <span class="text-xs text-blue-600 dark:text-blue-400 font-semibold">{{ $corridaInfo['hora_salida'] }}</span>
                    </div>

                    <div class="flex-1 flex items-center gap-1 min-w-12">
                        <div class="flex-1 h-px border-t-2 border-dashed border-gray-300 dark:border-neutral-600"></div>
                        <flux:icon.truck class="size-4 text-gray-400 shrink-0" />
                        <div class="flex-1 h-px border-t-2 border-dashed border-gray-300 dark:border-neutral-600"></div>
                    </div>

                    <div class="flex flex-col">
                        <span class="font-bold text-gray-800 dark:text-white text-sm leading-tight">{{ $corridaInfo['destino'] }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ $corridaInfo['hora_llegada'] }}</span>
                    </div>
                    <div class="flex flex-col items-center gap-1">
                        <span class="flex items-center justify-center w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-900/40 border-2 border-emerald-300 dark:border-emerald-700">
                            <flux:icon.flag class="size-4 text-emerald-600 dark:text-emerald-400" />
                        </span>
                        <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase">Destino</span>
                    </div>
                </div>

                <div class="hidden lg:block w-px bg-gray-100 dark:bg-neutral-800 self-stretch"></div>

                <div class="flex flex-wrap gap-5">
                    {{-- Unidad --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase">Unidad</span>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                            <span class="font-bold text-gray-800 dark:text-white text-sm">{{ $corridaInfo['urban'] }}</span>
                        </div>
                    </div>

                    {{-- Conductor --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase">Conductor</span>
                        <div class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
                                <flux:icon.user class="size-3.5 text-gray-500 dark:text-gray-400" />
                            </span>
                            <span class="font-semibold text-gray-800 dark:text-white text-sm">{{ $corridaInfo['conductor'] }}</span>
                        </div>
                    </div>

                    {{-- Tarifa --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase">Tarifa</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400 text-xl leading-none">${{ $corridaInfo['tarifa'] }}</span>
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
                    <div class="h-full rounded-full transition-all {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 60 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- DIV 2 — Lista de Asientos --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40">
                    <flux:icon.squares-2x2 class="size-4 text-blue-600 dark:text-blue-400" />
                </span>
                <h3 class="font-bold text-gray-800 dark:text-white text-base">Asientos</h3>
            </div>
        </div>

        @if(empty($asientos))
        <div class="flex flex-col items-center justify-center py-14 text-center">
            <flux:icon.squares-2x2 class="size-7 text-gray-400 mb-3" />
            <p class="text-gray-500 dark:text-gray-400 font-medium">Sin asientos registrados</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-neutral-800/60">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Asiento</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Pasajero</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Costo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                    @foreach($asientos as $asiento)
                    <tr wire:key="asiento-{{ $asiento['id'] }}" class="hover:bg-gray-50 dark:hover:bg-neutral-800/50">
                        <td class="px-4 py-3 font-bold text-gray-800 dark:text-white">{{ $asiento['nombre'] }}</td>
                        <td class="px-4 py-3">
                            <flux:badge color="{{ $asiento['estado'] === 'libre' ? 'green' : ($asiento['estado'] === 'apartado' ? 'amber' : 'red') }}">
                                {{ ucfirst($asiento['estado']) }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3">
                            @if($asiento['pasajero'])
                            <div class="flex items-center gap-2">
                                <flux:icon.user class="size-3 text-gray-500" />
                                <span class="text-gray-700 dark:text-gray-300">{{ $asiento['pasajero'] }}</span>
                            </div>
                            @else
                            <span class="italic text-gray-400 text-xs">Libre</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold">${{ $asiento['costo'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    @else
    <div class="flex flex-col items-center justify-center py-20 text-center bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200">
        <flux:icon.truck class="size-8 text-red-400 mb-4" />
        <p class="font-bold text-gray-700 dark:text-gray-300">Corrida no encontrada</p>
    </div>
    @endif
</div>