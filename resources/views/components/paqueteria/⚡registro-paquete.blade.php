<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Corrida;

new class extends Component
{
    public $fechaEnvio;
    public $corridaId   = null;
    public $corridaInfo = null; // ✅ Guarda info para pasarla al hijo

    public function mount()
    {
        $this->fechaEnvio = today()->format('Y-m-d');
    }

    #[On('corrida-seleccionada')]
    public function actualizarCorrida($id)
    {
        $this->corridaId = $id;

        // ✅ Obtiene la info de la corrida para el subheading
        $corrida = Corrida::with('ruta')->find($id);
        $this->corridaInfo = $corrida ? [
            'ruta'    => $corrida->ruta?->nombre ?? 'Sin ruta',
            'salida'  => $corrida->hora_salida ?? '—',
        ] : null;
    }

    #[On('corrida-deseleccionada')]
    public function quitarCorrida()
    {
        $this->corridaId   = null;
        $this->corridaInfo = null;
    }

    public function with(): array
    {
        return [];
    }
};
?>

<div class="p-6 space-y-6">

    <flux:card class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="lg">Registro de Paquetería</flux:heading>
            <flux:subheading>Seleccione fecha y corrida</flux:subheading>
        </div>
        <div class="w-full sm:w-64"> {{-- ← ancho completo en móvil --}}
            <flux:input type="date" label="Fecha de Envío" wire:model.live="fechaEnvio" />
        </div>
    </flux:card>

    @livewire('corrida.tabla-detalles-general', [
    'modo' => 'seleccion',
    'filtroFecha' => $fechaEnvio
    ], key($fechaEnvio))


    @if($corridaId)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            @livewire('paqueteria.form-paquete', [
            'corridaInfo' => $corridaInfo
            ], key('form-' . $corridaId))
        </div>
        <div class="lg:col-span-1">
            {{-- PASAMOS EL ID AQUÍ --}}
            @livewire('paqueteria.resumen-pago', [
            'corridaId' => $corridaId
            ], key('resumen-' . $corridaId))
        </div>
    </div>
    @endif

</div>