<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\Socio;

new class extends Component {
    public ?Socio $socio = null;

    #[Validate('required', message: 'El nombre es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    #[Validate('max:18', message: 'El nombre debe tener como maximo 18 caracteres.')]
    #[Validate('regex:/^[\pL\s\-]+$/u', message: 'El nombre debe contener solo letras.')]
    public $nombre;
    #[Validate('', message: '')]
    #[Validate('nullable', message: '')]
    #[Validate('min:3', message: 'El apellido paterno debe tener al menos 3 caracteres.')]
    #[Validate('max:12', message: 'El apellido paterno debe tener como maximo 12 caracteres.')]
    #[Validate('regex:/^[\pL\s\-]+$/u', message: 'El apellido paterno debe contener solo letras.')]
    public $apellido_paterno;

    #[Validate('nullable', message: '')]
    #[Validate('min:3', message: 'El apellido materno debe tener al menos 3 caracteres.')]
    #[Validate('max:12', message: 'El apellido materno debe tener como maximo 12 caracteres.')]
    #[Validate('regex:/^[\pL\s\-]+$/u', message: 'El apellido materno debe contener solo letras.')]
    public $apellido_materno;

    #[Validate('required', message: 'El estado es requerido.')]
    public $estado = '';

    #[Validate('required', message: 'La fecha de incorporación es requerida.')]
    #[Validate('date', message: 'La fecha de incorporación debe ser una fecha válida.')]
    public $fecha_de_incorporacion;

    #[Validate('required', message: 'El número telefónico es requerido.')]
    #[Validate('numeric', message: 'El número telefónico debe ser un valor numérico.')]
    #[Validate('min:10', message: 'El número telefónico debe tener al menos 10 dígitos.')]
    public $numero_telefonico;

    #[Validate('nullable')]
    #[Validate('email', message: 'Ingresa un correo valido')]
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
                <flux:field>
                    <flux:label badge="Obligatorio">Nombre(s)</flux:label>
                    <flux:input wire:model.live.blur="nombre" icon:trailing="a-large-small" type="text"
                        description:trailing="Ingrese minimo 3 caracteres" />
                    <flux:error name="nombre" />
                </flux:field>

                <flux:field>
                    <flux:label badge="Opcional">Apellido Paterno</flux:label>
                    <flux:input wire:model.live.blur="apellido_paterno" icon:trailing="a-large-small"
                        description:trailing="Ingrese minimo 3 caracteres" />
                    <flux:error name="apellido_paterno" />
                </flux:field>

                <flux:field>
                    <flux:label badge="Opcional">Apellido Materno</flux:label>
                    <flux:input wire:model.live.blur="apellido_materno" icon:trailing="a-large-small"
                        description:trailing="Ingrese minimo 3 caracteres" />
                    <flux:error name="apellido_materno" />
                </flux:field>

                <flux:field>
                    <flux:label badge="Obligatorio">Estado</flux:label>
                    <flux:select wire:model="estado">
                        <flux:select.option value="Activo">Activo</flux:select.option>
                        <flux:select.option value="Inactivo">Inactivo</flux:select.option>
                    </flux:select>
                    <flux:error name="estado" />
                </flux:field>

                <flux:field>
                    <flux:label badge="Obligatorio">Fecha de Incorporación</flux:label>
                    <flux:input wire:model="fecha_de_incorporacion" type="date" />
                    <flux:error name="fecha_de_incorporacion" />
                </flux:field>

                <flux:field>
                    <flux:label badge="Obligatorio">Teléfono</flux:label>
                    <flux:input wire:model="numero_telefonico" />
                    <flux:error name="numero_telefonico" />
                </flux:field>

                <flux:field>
                    <flux:label badge="Opcional">Correo Electrónico</flux:label>
                    <flux:input wire:model="correo" />
                    <flux:error name="correo" />
                </flux:field>
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
                    ¿Estás seguro de que deseas eliminar a <b>'{{ $socio->nombre }} {{ $socio->apellido_paterno }}
                        {{ $socio->apellido_materno }}'</b>?
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