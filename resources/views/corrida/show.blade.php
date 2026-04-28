<x-layouts::app :title="__('Detalle de corrida')">
    @livewire('corrida.detalle-corrida', ['corridaId' => $id])
</x-layouts::app>