<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use App\Models\Cliente;

new class extends Component
{
    #[Validate('required', message: 'El nombre es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    public $nombre = null;

    #[Validate('required', message: 'El apellido paterno es requerido.')]
    #[Validate('min:3', message: 'El apellido paterno debe tener al menos 3 caracteres.')]
    public $apellido_paterno = null;

    #[Validate('min:3', message: 'El apellido materno debe tener al menos 3 caracteres.')]
    #[Validate('nullable')]
    public $apellido_materno = null;

    public function save(){
        $this->validate();

        Cliente::create([
            'nombre' => $this->nombre,
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
        ]);

        $this->reset(['nombre', 'apellido_paterno', 'apellido_materno']);

        $this->dispatch('cliente-creado');
        session()->flash('status', 'Cliente creado correctamente');
    }
};
?>

<form wire:submit="save" class="p-6">
    <flux:card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <flux:field>
                <flux:label badge="Obligatorio">Nombre(s)</flux:label>
                <flux:input wire:model.live.blur="nombre" icon:trailing="a-large-small" type="text"
                    description:trailing="Ingrese minimo 3 caracteres" />
                <flux:error name="nombre" />
            </flux:field>

            <flux:field>
                <flux:label badge="Obligatorio">Apellido Paterno</flux:label>
                <flux:input wire:model.live.blur="apellido_paterno" icon:trailing="a-large-small" type="text"
                    description:trailing="Ingrese minimo 3 caracteres" />
                <flux:error name="apellido_paterno" />
            </flux:field>

            <flux:field>
                <flux:label badge="Opcional">Apellido Materno</flux:label>
                <flux:input wire:model.live.blur="apellido_materno" icon:trailing="a-large-small" type="text"
                    description:trailing="Ingrese minimo 3 caracteres" />
                <flux:error name="apellido_materno" />
            </flux:field>
        </div>

        <div class="mt-8">
            <flux:button type="submit" variant="primary" class="w-full">Crear Cliente</flux:button>
        </div>
    </flux:card>
</form>