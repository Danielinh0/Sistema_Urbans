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

        $boletos = collect(range(1, 12))->map(
            fn($m) =>
            Boleto::whereHas('boletoCliente')
                ->whereHas(
                    'detalleVenta.venta',
                    fn($q) =>
                    $q->whereYear('fecha', $this->year)->whereMonth('fecha', $m)
                )->count()
        );

        $paquetes = collect(range(1, 12))->map(
            fn($m) =>
            Boleto::whereHas('boletoPaquete')
                ->whereHas(
                    'detalleVenta.venta',
                    fn($q) =>
                    $q->whereYear('fecha', $this->year)->whereMonth('fecha', $m)
                )->count()
        );

        return [
            'meses'    => $meses,
            'boletos'  => $boletos->values()->toArray(),
            'paquetes' => $paquetes->values()->toArray(),
            'years'    => range(now()->year - 3, now()->year),
        ];
    }
};
?>

<div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex flex-col h-full">

    <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex-shrink-0">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/40">
                <flux:icon name="tickets" class="size-4 text-violet-600 dark:text-violet-400" />
            </span>
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white text-base">Boletos por mes</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-medium">
                    Urbán y paquetería
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <flux:select wire:model.live="year" class="w-28 text-sm">
                @foreach($years as $y)
                <flux:select.option value="{{ $y }}">{{ $y }}</flux:select.option>
                @endforeach
            </flux:select>

            {{-- ✅ Botón PDF píldora azul --}}
            <button class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-full shadow-sm transition-colors duration-150">
                <flux:icon name="pdf-icon" class="size-4" />
                PDF
            </button>
        </div>
    </div>

    <div class="flex-1 p-6 min-h-0"
        wire:key="grafica-boletos-{{ $year }}"
        x-data="{
            chart: null,
            init() {
                if (this.chart) this.chart.destroy();
                this.chart = new Chart(this.$refs.canvas, {
                    type: 'bar',
                    data: {
                        labels: @js($meses),
                        datasets: [
                            {
                                label: 'Urbán',
                                data: @js($boletos),
                                backgroundColor: 'rgba(59,130,246,0.8)',
                                borderRadius: 4,
                            },
                            {
                                label: 'Paquetes',
                                data: @js($paquetes),
                                backgroundColor: 'rgba(139,92,246,0.8)',
                                borderRadius: 4,
                            }
                        ]
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