<?php

use App\Models\Corrida;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Reactive;

new class extends Component
{
    public string $modo = 'vista';
    #[Reactive]
    public string $filtroFecha = '';

    public bool $permitirCambiarFecha = true;

    public string $filtroRuta = '';
    public ?int $corridaSeleccionadaId = null;

    public function mount(): void
    {
        // Inicializamos con el día de hoy
        if (empty($this->filtroFecha) && $this->permitirCambiarFecha) {
            $this->filtroFecha = today()->format('Y-m-d');
        }
    }

    public function seleccionar(int $id): void
    {
        if ($this->modo !== 'seleccion') return;

        if ($this->corridaSeleccionadaId === $id) {
            $this->corridaSeleccionadaId = null;
            $this->dispatch('corrida-deseleccionada');
            return;
        }

        $this->corridaSeleccionadaId = $id;
        $this->dispatch('corrida-seleccionada', id: $id);
    }

    private function mapearCorrida(Corrida $corrida): array
    {
        $total    = $corrida->urban?->numero_asientos ?? 0;
        $vendidos = $corrida->boletos->count();
        $libres   = max(0, $total - $vendidos);

        // Ahora usamos los objetos Carbon directamente de las columnas dateTime
        $salida  = $corrida->datetime_salida;
        $llegada = $corrida->datetime_llegada;

        // Lógica de estado basada en la columna 'estado' de la BD y tiempos
        $estadoActual = $corrida->estado; // 'Activa', 'Finalizada', etc.

        if ($salida->isPast() && $estadoActual !== 'Finalizada') {
            $estadoActual = 'En Camino';
        }

        return [
            'id'             => $corrida->id_corrida,
            'hora_salida'    => $salida->format('g:i A'),
            'hora_llegada'   => $llegada ? $llegada->format('g:i A') : '—',
            'ruta'           => $corrida->ruta?->nombre ?? 'Sin ruta',
            'codigo_urban'   => $corrida->urban?->codigo_urban ?? '—',
            'chofer'         => $corrida->user?->name ?? 'No asignado',
            // Cambiamos 'precio' por 'tarifa_clientes' según tu migración de Ruta
            'tarifa'         => number_format($corrida->ruta?->tarifa_clientes ?? 0, 2),
            'tarifa_raw'     => $corrida->ruta?->tarifa_clientes ?? 0,
            'libres'         => $libres,
            'lleno'          => $total > 0 && $libres === 0,
            'estado'         => $estadoActual,
        ];
    }

    public function with(): array
    {
        $fechaConsulta = $this->filtroFecha ?: today()->format('Y-m-d');

        $corridas = Corrida::with(['ruta', 'urban', 'boletos', 'user'])
            ->whereDate('datetime_salida', $fechaConsulta)
            ->when($this->filtroRuta, function ($q) {
                $q->whereHas('ruta', fn($r) => $r->where('nombre', 'like', "%{$this->filtroRuta}%"));
            })
            ->orderBy('datetime_salida', 'asc')
            ->get()
            ->map(fn($c) => $this->mapearCorrida($c))
            ->toArray();

        return [
            'corridas' => $corridas,
            'fechaFormateada' => Carbon::parse($fechaConsulta)
                ->locale('es')
                ->isoFormat('ddd D [de] MMMM, YYYY'),
        ];
    }
}; ?>

<div class="space-y-6">
    {{-- ── Tabla de Corridas Disponibles ─────────────────────────────── --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">

        {{-- Header de la Tabla --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40">
                    <flux:icon.clock-fading class="size-4 text-blue-600 dark:text-blue-400" />
                </span>
                <div>
                    <h3 class="font-bold text-gray-800 dark:text-white text-base">Corridas Disponibles</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-medium">
                        {{ $fechaFormateada }}
                    </p>
                </div>
            </div>
        </div>

        @if(empty($corridas))
        <div class="flex flex-col items-center justify-center py-14 text-center">
            <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-neutral-800 flex items-center justify-center mb-3">
                <flux:icon.bus class="size-7 text-gray-400" />
            </div>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Sin corridas para esta fecha</p>
            @if($modo === 'seleccion')
            <p class="text-gray-300 dark:text-gray-200 font-xs">Intenta seleccionar otra fecha</p>
            @endif
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-neutral-800/60 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-4 py-3 text-left">Salida</th>
                        <th class="px-4 py-3 text-left">Llegada</th>
                        <th class="px-4 py-3 text-left">Unidad</th>
                        <th class="px-4 py-3 text-left">Chofer</th>
                        <th class="px-4 py-3 text-left">Destino</th>
                        <th class="px-4 py-3 text-center">Asientos</th>
                        <th class="px-4 py-3 text-right">Tarifa</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                    @foreach($corridas as $corrida)
                    <tr wire:key="corrida-{{ $corrida['id'] }}"
                        @if($modo==='seleccion' ) wire:click="seleccionar({{ $corrida['id'] }})" @endif
                        class="transition-colors duration-150 cursor-pointer
                                {{ $corridaSeleccionadaId === $corrida['id'] 
                                    ? 'bg-blue-50 dark:bg-blue-900/20 ring-1 ring-inset ring-blue-300 dark:ring-blue-700' 
                                    : 'hover:bg-gray-50 dark:hover:bg-neutral-800/50' }}">

                        <td class="px-4 py-3 font-bold text-gray-800 dark:text-white">
                            {{ $corrida['hora_salida'] }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                            {{ $corrida['hora_llegada'] }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 font-semibold text-gray-700 dark:text-gray-300">
                                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                {{ $corrida['codigo_urban'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            {{ $corrida['chofer'] }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800 dark:text-white">
                            {{ $corrida['ruta'] }}
                        </td>

                        {{-- Asientos badge --}}
                        <td class="px-4 py-3 text-center">
                            @if($corrida['lleno'])
                            <flux:badge color="red" size="sm" inset="top bottom">Lleno</flux:badge>
                            @elseif($corrida['libres'] <= 3)
                                <flux:badge color="amber" size="sm" inset="top bottom">{{ $corrida['libres'] }} Libres</flux:badge>
                                @else
                                <flux:badge color="green" size="sm" inset="top bottom">{{ $corrida['libres'] }} Libres</flux:badge>
                                @endif
                        </td>

                        <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white">
                            ${{ $corrida['tarifa'] }}
                        </td>

                        {{-- Estado badge --}}
                        <td class="px-4 py-3 text-center">
                            @php
                            $estadoColor = match($corrida['estado']) {
                            'En Camino' => 'amber',
                            'Finalizado' => 'zinc',
                            default => 'blue',
                            };
                            @endphp
                            <flux:badge :color="$estadoColor" size="sm">{{ $corrida['estado'] }}</flux:badge>
                        </td>

                        {{-- Botón acción --}}
                        <td class="px-4 py-3 text-right">
                            @if($modo === 'vista')
                            <flux:button href="{{ route('corrida.show', $corrida['id']) }}" size="sm" variant="outline" icon-trailing="chevron-right">
                                Detalles
                            </flux:button>
                            @else
                            @if($corridaSeleccionadaId === $corrida['id'])
                            <flux:button size="sm" variant="primary">✓ Elegida</flux:button>
                            @else
                            <flux:button size="sm" variant="outline">Seleccionar</flux:button>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>