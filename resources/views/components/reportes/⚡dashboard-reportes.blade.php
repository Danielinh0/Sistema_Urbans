<?php

use Livewire\Component;

new class extends Component {
    public function with(): array
    {
        return [];
    }
};
?>

<div class="p-6 space-y-6">

    {{-- Fila 1: Resumen del día --}}
    @livewire('reportes.resumen-hoy')

    {{-- Fila 2: Gráfica principal (80%) + Taquillas (20%) --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:h-[400px]">
        <div class="lg:col-span-4 min-h-0">
            @livewire('reportes.grafica-corridas')
        </div>
        <div class="lg:col-span-1 min-h-0">
            @livewire('reportes.widgets-taquillas')
        </div>
    </div>

    {{-- Fila 3: Boletos por mes (50%) + Rutas por mes (50%) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @livewire('reportes.grafica-boletos-mes')
        @livewire('reportes.grafica-rutas-mes')
    </div>

</div>