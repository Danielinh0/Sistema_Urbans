<?php

use Livewire\Component;
use App\Models\Boleto;
use App\Models\Venta;
use App\Models\Corrida;

new class extends Component
{
    public function with(): array
    {
        $baseQuery  = fn($q) => $q->whereDate('fecha', today());
        $totalHoy   = Venta::whereDate('fecha', today())->sum('total');
        $boletosHoy = Boleto::whereHas('boletoCliente')->whereHas('detalleVenta.venta', $baseQuery)->count();
        $paquetesHoy = Boleto::whereHas('boletoPaquete')->whereHas('detalleVenta.venta', $baseQuery)->count();
        $corridasHoy = Corrida::whereDate('datetime_salida', today())->count();

        return compact('totalHoy', 'boletosHoy', 'paquetesHoy', 'corridasHoy');
    }
};
?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach([
    ['icon' => 'chart-candlestick', 'color' => 'emerald', 'label' => 'Ventas hoy', 'value' => '$' . number_format($totalHoy, 2)],
    ['icon' => 'tickets', 'color' => 'blue', 'label' => 'Boletos urbán', 'value' => $boletosHoy],
    ['icon' => 'package', 'color' => 'violet', 'label' => 'Paquetes', 'value' => $paquetesHoy],
    ['icon' => 'bus', 'color' => 'amber', 'label' => 'Corridas hoy', 'value' => $corridasHoy],
    ] as $card)
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm px-5 py-4 flex items-center gap-4">
        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-900/40 flex-shrink-0">
            <flux:icon name="{{ $card['icon'] }}" class="size-5 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400" />
        </span>
        <div>
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-medium">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $card['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>