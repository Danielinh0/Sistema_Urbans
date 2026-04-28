<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use App\Models\Cliente;

new class extends Component
{
    public ?Cliente $cliente = null;

    #[Validate('required', message: 'El nombre es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    public $nombre = null;

    #[Validate('required', message: 'El apellido paterno es requerido.')]
    #[Validate('min:3', message: 'El apellido paterno debe tener al menos 3 caracteres.')]
    public $apellido_paterno = null;

    #[Validate('required', message: 'El apellido materno es requerido.')]
    #[Validate('min:3', message: 'El apellido materno debe tener al menos 3 caracteres.')]
    public $apellido_materno = null;

    #[On('preparar-edicion-cliente')]
    public function prepararEdicion($id)
    {
        $this->cliente = Cliente::findOrFail($id);
        $this->nombre = $this->cliente->nombre;
        $this->apellido_paterno = $this->cliente->apellido_paterno;
        $this->apellido_materno = $this->cliente->apellido_materno;
        $this->js("Flux.modal('modal-editar-cliente').show()");
    }

    #[On('preparar-eliminacion-cliente')]
    public function prepararEliminacion($id){
        $this->cliente = Cliente::findOrFail($id);
        $this->js("Flux.modal('modal-eliminar-cliente').show()");
    }

    public function update(){
        $this->validate();

        $this->cliente->update([
            'nombre' => $this->nombre,
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
        ]);
        $this->js("Flux.modal('modal-editar-cliente').close()");
        $this->dispatch('cliente-actualizado');
    }

    public function delete()
    {
        $this->cliente->delete();

        $this->js("Flux.modal('modal-eliminar-cliente').close()");
        $this->dispatch('cliente-eliminado');
    }
};
?>

<div>
    <flux:modal name="modal-editar-cliente" class="w-[60%] p-10">
        @if ($cliente)
            <flux:heading class="!text-xl !font-bold" size="lg">
                Editar cliente: {{ $cliente->nombre }} {{ $cliente->apellido_paterno }} {{ $cliente->apellido_materno }}
            </flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mt-6">
                <flux:field>
                    <flux:label badge="Obligatorio">Nombre(s)</flux:label>
                    <flux:input wire:model.live.blur="nombre" icon:trailing="a-large-small"
                        description:trailing="Ingrese minimo 3 caracteres" />
                    <flux:error name="nombre" />
                </flux:field>

                <flux:field>
                    <flux:label badge="Obligatorio">Apellido Paterno</flux:label>
                    <flux:input wire:model.live.blur="apellido_paterno" icon:trailing="a-large-small"
                        description:trailing="Ingrese minimo 3 caracteres" />
                    <flux:error name="apellido_paterno" />
                </flux:field>

                <flux:field>
                    <flux:label badge="Obligatorio">Apellido Materno</flux:label>
                    <flux:input wire:model.live.blur="apellido_materno" icon:trailing="a-large-small"
                        description:trailing="Ingrese minimo 3 caracteres" />
                    <flux:error name="apellido_materno" />
                </flux:field>

            </div>

            <div class="mt-8">
                <flux:button wire:click="update" variant="primary" class="w-full">Guardar Cambios</flux:button>
            </div>
        @endif
    </flux:modal>

    <flux:modal name="modal-eliminar-cliente" class="min-w-[22rem]">
        @if($cliente)
            <div class="space-y-6">
                <flux:heading size="lg">Eliminar Cliente</flux:heading>
                <flux:text>
                    ¿Estás seguro de que deseas eliminar al cliente
                    <b>{{ $cliente->nombre }} {{ $cliente->apellido_paterno }} {{ $cliente->apellido_materno }}</b>?
                </flux:text>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:button wire:click="delete" variant="danger">Eliminar</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>