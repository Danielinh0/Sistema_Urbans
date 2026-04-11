<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\Socio;

new class extends Component {
    public ?Socio $socio = null;

    // Propiedades del formulario (extraídas de tu form.blade.php)
    #[Validate('required', message: 'El nombre es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    public $nombre;

    #[Validate('required', message: 'El apellido paterno es requerido.')]
    public $apellido_paterno;

    #[Validate('required', message: 'El apellido materno es requerido.')]
    public $apellido_materno;

    #[Validate('required', message: 'El estado es requerido.')]
    public $estado;

    #[Validate('required', message: 'La fecha de incorporación es requerida.')]
    public $fecha_de_incorporacion;

    #[Validate('required', message: 'El número telefónico es requerido.')]
    public $numero_telefonico;

    #[Validate('required', message: 'El correo es requerido.')]
    #[Validate('email', message: 'El correo debe ser válido.')]
    public $correo;

    #[On('preparar-edicion-socio')]
    public function prepararEdicion($id)
    {
        $this->socio = Socio::findOrFail($id);
        $this->nombre = $this->socio->nombre;
        $this->apellido_paterno = $this->socio->apellido_paterno;
        $this->apellido_materno = $this->socio->apellido_materno;
        $this->estado = $this->socio->estado;
        $this->fecha_de_incorporacion = $this->socio->fecha_de_incorporacion;
        $this->numero_telefonico = $this->socio->numero_telefonico;
        $this->correo = $this->socio->correo;

        $this->js("Flux.modal('modal-editar-socio').show()");
    }

    #[On('preparar-eliminacion-socio')]
    public function prepararEliminacion($id)
    {
        $this->socio = Socio::findOrFail($id);
        $this->js("Flux.modal('modal-eliminar-socio').show()");
    }

    public function update()
    {
        $this->validate();

        $this->socio->update([
            'nombre' => $this->nombre,
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
            'estado' => $this->estado,
            'fecha_de_incorporacion' => $this->fecha_de_incorporacion,
            'numero_telefonico' => $this->numero_telefonico,
            'correo' => $this->correo,
        ]);

        $this->js("Flux.modal('modal-editar-socio').close()");
        $this->dispatch('socio-creado');
    }

    public function delete()
    {
        $this->socio->delete();
        $this->js("Flux.modal('modal-eliminar-socio').close()");
        $this->dispatch('socio-eliminado');
    }
};
?>

<div>
    <flux:modal name="modal-editar-socio" class="w-[60%] p-10">
        @if($socio)
            <flux:heading size="lg">Editar Socio: {{ $socio->nombre }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mt-6">
                <flux:input wire:model="nombre" label="Nombre" />
                <flux:input wire:model="apellido_paterno" label="Apellido Paterno" />
                <flux:input wire:model="apellido_materno" label="Apellido Materno" />

                <flux:select wire:model="estado" label="Estado">
                    <flux:select.option>Activo</flux:select.option>
                    <flux:select.option>Inactivo</flux:select.option>
                </flux:select>

                <flux:input wire:model="fecha_de_incorporacion" type="date" label="Incorporación" />
                <flux:input wire:model="numero_telefonico" label="Teléfono" />

                <div class="md:col-span-2">
                    <flux:input wire:model="correo" label="Correo Electrónico" />
                </div>
            </div>

            <div class="mt-8">
                <flux:button wire:click="update" variant="primary" class="w-full">Guardar Cambios</flux:button>
            </div>
        @endif
    </flux:modal>

    <flux:modal name="modal-eliminar-socio" class="min-w-[22rem]">
        @if($socio)
            <div class="space-y-6">
                <flux:heading size="lg">Eliminar Socio</flux:heading>
                <flux:text>
                    ¿Estás seguro de que deseas eliminar a <b>{{ $socio->nombre }} {{ $socio->apellido_paterno }}'
                        '{{ $socio->apellido_materno }}</b>?
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