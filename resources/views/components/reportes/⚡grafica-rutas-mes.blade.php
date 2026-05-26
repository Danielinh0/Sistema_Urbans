<?php

use Livewire\Component;
use App\Models\Boleto;

new class extends Component
{
    public int $year;

    public function mount()
    {
        $this->year = now()->year;
    }

    public function with(): array
    {
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        $topRutas = Boleto::with('corrida.ruta')
            ->whereHas('detalleVenta.venta', fn($q) => $q->whereYear('fecha', $this->year))
            ->get()
            ->groupBy(fn($b) => $b->corrida?->ruta?->nombre ?? 'Sin ruta')
            ->map->count()
            ->sortDesc()
            ->take(5)
            ->keys();

        $colores = [
            'rgba(59,130,246,1)',
            'rgba(139,92,246,1)',
            'rgba(16,185,129,1)',
            'rgba(245,158,11,1)',
            'rgba(239,68,68,1)',
        ];

        $datasets = $topRutas->values()->map(function ($ruta, $i) use ($colores) {
            $data = collect(range(1, 12))->map(
                fn($m) =>
                Boleto::whereHas('corrida.ruta', fn($q) => $q->where('nombre', $ruta))
                    ->whereHas(
                        'detalleVenta.venta',
                        fn($q) =>
                        $q->whereYear('fecha', $this->year)->whereMonth('fecha', $m)
                    )->count()
            )->values()->toArray();

            return [
                'label'           => $ruta,
                'data'            => $data,
                'borderColor'     => $colores[$i] ?? 'rgba(100,100,100,1)',
                'backgroundColor' => str_replace(',1)', ',0.1)', $colores[$i] ?? 'rgba(100,100,100,0.1)'),
                'tension'         => 0.4,
                'fill'            => true,
                'pointRadius'     => 3,
            ];
        })->values()->toArray();

        return [
            'meses'    => $meses,
            'datasets' => $datasets,
            'years'    => range(now()->year - 3, now()->year),
        ];
    }
};
?>

<div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex flex-col h-full">

    <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex-shrink-0">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40">
                <flux:icon name="map-pinned" class="size-4 text-emerald-600 dark:text-emerald-400" />
            </span>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white text-base">Rutas por mes</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-medium">
                    Top 5 rutas más vendidas
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <flux:select wire:model.live="year" class="w-28 text-sm">
                @foreach($years as $y)
                <flux:select.option value="{{ $y }}">{{ $y }}</flux:select.option>
                @endforeach
            </flux:select>

        </div>
    </div>

    <div class="flex-1 p-6 min-h-0"
        wire:key="grafica-rutas-{{ $year }}"
        x-data="{
            chart: null,
            init() {
                if (this.chart) this.chart.destroy();
                this.chart = new Chart(this.$refs.canvas, {
                    type: 'line',
                    data: {
                        labels: @js($meses),
                        datasets: @js($datasets)
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top' } },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(156,163,175,0.1)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        }"
        x-init="init()">
        <canvas x-ref="canvas" class="w-full h-full"></canvas>
    </div>

</div>