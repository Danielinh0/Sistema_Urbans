<?php

use Livewire\Component;
use App\Models\Taquilla;

new class extends Component
{
    public function with(): array
    {
        $taquillas = Taquilla::with([
            'turnos' => fn($q) =>
            $q->with('user')->whereDate('fecha', today())->latest('hora_inicio')
        ])->get()->map(function ($t) {
            $turno           = $t->turnos->first();
            $diferencia      = $turno ? ($t->monto_actual - $turno->monto_inicial) : 0;
            $t->taquillero   = $turno?->user?->name ?? null;
            $t->diferencia   = $diferencia;
            $t->tendencia    = $diferencia > 0 ? 'up' : ($diferencia < 0 ? 'down' : 'neutral');
            $t->esta_abierta = $turno && is_null($turno->hora_fin);
            return $t;
        });

        return ['taquillas' => $taquillas];
    }
};
?>

<div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex flex-col h-full">

    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex-shrink-0">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40">
                <flux:icon name="tickets" class="size-4 text-blue-600 dark:text-blue-400" />
            </span>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white text-base">Taquillas</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-medium">
                    Estado actual
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                {{ $taquillas->where('esta_abierta', true)->count() }} abiertas
            </span>
        </div>
    </div>

    {{-- Lista con scroll --}}
    <div class="flex-1 overflow-y-auto divide-y divide-neutral-100 dark:divide-neutral-800 min-h-0">
        @forelse($taquillas as $t)
        <div class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-neutral-800/50 transition-colors duration-150">

            {{-- Izquierda: indicador + nombre + cajero --}}
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex-shrink-0 w-2 h-2 rounded-full
                        {{ $t->esta_abierta ? 'bg-emerald-400' : 'bg-neutral-300 dark:bg-neutral-600' }}">
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">
                        {{ $t->nombre ?? 'Taquilla #' . $t->id_taquilla }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">
                        {{ $t->taquillero ?? 'Sin cajero asignado' }}
                    </p>
                </div>
            </div>

            {{-- Derecha: monto + tendencia --}}
            <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-800 dark:text-white">
                        ${{ number_format($t->monto_actual, 0) }}
                    </p>
                    @if($t->diferencia != 0)
                    <p class="text-xs {{ $t->tendencia === 'up' ? 'text-emerald-500' : 'text-red-400' }}">
                        {{ $t->tendencia === 'up' ? '+' : '' }}${{ number_format($t->diferencia, 0) }}
                    </p>
                    @endif
                </div>

                @if($t->tendencia === 'up')
                <flux:icon name="square-plus" class="size-4 text-emerald-500 flex-shrink-0" />
                @elseif($t->tendencia === 'down')
                <flux:icon name="circle-minus" class="size-4 text-red-400 flex-shrink-0" />
                @else
                <flux:icon name="minus" class="size-4 text-neutral-300 dark:text-neutral-600 flex-shrink-0" />
                @endif
            </div>

        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-14 text-center">
            <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-neutral-800 flex items-center justify-center mb-3">
                <flux:icon name="tickets" class="size-7 text-gray-400" />
            </div>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Sin taquillas registradas</p>
        </div>
        @endforelse
    </div>

</div>