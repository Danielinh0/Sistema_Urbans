<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Socio;

new class extends Component {
    #[Validate('required', message: 'El nombre es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    #[Validate('max:18', message: 'El nombre debe tener como maximo 18 caracteres.')]
    public $nombre;

    #[Validate('nullable|min:3|regex:/^[\pL\s\-]+$/u', message: 'El apellido paterno debe tener al menos 3 caracteres.')]
    #[Validate('max:12', message: 'El apellido paterno debe tener como maximo 12 caracteres.')]
    public $apellido_paterno;

    #[Validate('nullable|min:3|regex:/^[\pL\s\-]+$/u', message: 'El apellido materno debe tener al menos 3 caracteres.')]
    #[Validate('max:12', message: 'El apellido materno debe tener como maximo 12 caracteres.')]
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

    #[Validate('nullable|email', message: 'El correo debe ser un correo válido.')]
    public $correo;

    public function save()
    {
        $this->validate();
        Socio::create([
            'nombre' => $this->nombre,
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
            'estado' => $this->estado,
            'fecha_de_incorporacion' => $this->fecha_de_incorporacion,
            'numero_telefonico' => $this->numero_telefonico,
            'correo' => $this->correo,
        ]);
        $this->reset();
        $this->dispatch('socio-creado');
        session()->flash('status', 'Socio creado correctamente.');
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
                <flux:select wire:model="estado" placeholder="Seleccione el estado">
                    <flux:select.option value="Activo">Activo</flux:select.option>
                    <flux:select.option value="Inactivo">Inactivo</flux:select.option>
                </flux:select>
                <flux:error name="estado" />
            </flux:field>

            <flux:field>
                <flux:label badge="Obligatorio">Fecha de Incorporación</flux:label>
                <flux:input wire:model.live.blur="fecha_de_incorporacion"
                    description:trailing="La fecha debe ser valida" type="date" />
                <flux:error name="fecha_de_incorporacion" />
            </flux:field>

            <flux:field>
                <flux:label badge="Obligatorio">Número Telefónico</flux:label>
                <flux:input wire:model.live.blur="numero_telefonico" icon:trailing="smartphone"
                    description:trailing="Ingrese un numero de telefono valido" />
                <flux:error name="numero_telefonico" />
            </flux:field>

            <flux:field>
                <flux:label badge="Opcional">Correo</flux:label>
                <flux:input wire:model.live.blur="correo" icon:trailing="mail"
                    description:trailing="Ingrese un correo electronico valido" />
                <flux:error name="correo" />
            </flux:field>
        </div>

        <div class="mt-8">
            <flux:button type="submit" variant="primary" class="w-full">Crear Socio</flux:button>
        </div>
    </flux:card>
</form>