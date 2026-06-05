<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Taquilla;

new class extends Component
{
    #[Validate('required', message: 'El monto inicial es requerido.')]
    #[Validate('numeric', message: 'El monto debe ser un valor numérico.')]
    #[Validate('min:0', message: 'El monto no puede ser negativo.')]
    public $monto_actual = null;

    public function create()
    {
        $this->validate();

        Taquilla::create([
            'monto_actual' => $this->monto_actual,
        ]);

        $this->reset('monto_actual');
        $this->js("Flux.modal('modal-crear-taquilla').close()");
        $this->dispatch('taquilla-creada');
    }
};
?>

<div>
    <flux:modal name="modal-crear-taquilla" class="min-w-[28rem] p-10">
        <flux:heading size="lg">Nueva Taquilla</flux:heading>
        <flux:subheading>Ingresa el monto inicial para la nueva taquilla.</flux:subheading>

        <div class="mt-6 space-y-4">
            <flux:input
                wire:model.live.blur="monto_actual"
                label="Monto Inicial"
                type="number"
                step="0.01"
                icon="currency-dollar"
                placeholder="0.00"
            />
        </div>

        <div class="flex gap-2 mt-8">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">Cancelar</flux:button>
            </flux:modal.close>
            <flux:button
                wire:click="create"
                variant="primary"
                class="!bg-blue-800 hover:!bg-blue-900 !border-blue-800 !text-white">
                Crear Taquilla
            </flux:button>
        </div>
    </flux:modal>

    
</div>