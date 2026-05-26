<?php

use Livewire\Component;
use App\Models\Boleto;

new class extends Component
{
    public function with(): array
    {
        $resultados = Boleto::with('corrida.ruta')
            ->whereHas(
                'detalleVenta.venta',
                fn($q) =>
                $q->whereDate('fecha', '>=', now()->subDays(30))
            )
            ->get()
            ->groupBy(fn($b) => $b->corrida?->ruta?->nombre ?? 'Sin ruta')
            ->map->count()
            ->sortDesc()
            ->take(7);

        return [
            'labels' => $resultados->keys()->values()->toArray(),
            'data'   => $resultados->values()->toArray(),
        ];
    }
};
?>

<div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200
            dark:border-neutral-700 shadow-sm overflow-hidden">

    <div class="flex items-center justify-between px-6 py-4 border-b
                border-neutral-100 dark:border-neutral-800">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg
                         bg-blue-100 dark:bg-blue-900/40">
                <flux:icon name="map" class="size-4 text-blue-600 dark:text-blue-400" />
            </span>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white text-base">Rutas más vendidas</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-medium">
                    Últimos 30 días
                </p>
            </div>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                     bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">
            Top 7
        </span>
    </div>

    <div class="p-4 sm:p-6"
        wire:key="grafica-corridas"
        x-data="{
            chart: null,
            init() {
                if (this.chart) { this.chart.destroy(); this.chart = null; }
                this.$nextTick(() => {
                    this.chart = new Chart(this.$refs.canvas, {
                        type: 'bar',
                        data: {
                            labels: @js($labels),
                            datasets: [{
                                label: 'Boletos vendidos',
                                data: @js($data),
                                backgroundColor: 'rgba(59,130,246,0.8)',
                                borderColor: 'rgba(59,130,246,1)',
                                borderWidth: 1,
                                borderRadius: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(156,163,175,0.15)' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                });
            }
        }"
        x-init="init()">
        <div class="relative h-64 sm:h-80">
            <canvas x-ref="canvas"></canvas>
        </div>
    </div>
</div>