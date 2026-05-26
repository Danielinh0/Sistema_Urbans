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
    [
    'icon' => 'chart-candlestick',
    'bg' => 'bg-emerald-100 dark:bg-emerald-900/40',
    'text' => 'text-emerald-600 dark:text-emerald-400',
    'label' => 'Ventas hoy',
    'value' => '$' . number_format($totalHoy, 2),
    ],
    [
    'icon' => 'tickets',
    'bg' => 'bg-blue-100 dark:bg-blue-900/40',
    'text' => 'text-blue-600 dark:text-blue-400',
    'label' => 'Boletos urbán',
    'value' => $boletosHoy,
    ],
    [
    'icon' => 'package',
    'bg' => 'bg-violet-100 dark:bg-violet-900/40',
    'text' => 'text-violet-600 dark:text-violet-400',
    'label' => 'Paquetes',
    'value' => $paquetesHoy,
    ],
    [
    'icon' => 'bus',
    'bg' => 'bg-amber-100 dark:bg-amber-900/40',
    'text' => 'text-amber-600 dark:text-amber-400',
    'label' => 'Corridas hoy',
    'value' => $corridasHoy,
    ],
    ] as $card)
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200
                dark:border-neutral-700 shadow-sm px-5 py-4 flex items-center gap-4">
        <span class="flex items-center justify-center w-10 h-10 rounded-xl flex-shrink-0 {{ $card['bg'] }}">
            <flux:icon name="{{ $card['icon'] }}" class="size-5 {{ $card['text'] }}" />
        </span>
        <div>
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-medium">
                {{ $card['label'] }}
            </p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $card['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>