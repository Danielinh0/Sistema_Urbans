<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Socio;

new class extends Component {
    #[Validate('required', message: 'El nombre es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    public $nombre;

    #[Validate('required', message: 'El apellido paterno es requerido.')]
    #[Validate('min:3', message: 'El apellido paterno debe tener al menos 3 caracteres.')]
    public $apellido_paterno;

    #[Validate('required', message: 'El apellido materno es requerido.')]
    #[Validate('min:3', message: 'El apellido materno debe tener al menos 3 caracteres.')]
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

    #[Validate('required', message: 'El correo es requerido.')]
    #[Validate('email', message: 'El correo debe ser un correo válido.')]
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
                <flux:label>Nombre del socio</flux:label>
                <flux:input wire:model.live.blur="nombre" icon:trailing="a-large-small" type="text"
                    label="Nombre del socio" description:trailing="Ingrese minimo 3 caracteres" />
                @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <flux:input wire:model.live.blur="apellido_paterno" icon:trailing="a-large-small"
                    label="Apellido Paterno" description:trailing="Ingrese minimo 3 caracteres" />
                @error('apellido_paterno') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <flux:input wire:model.live.blur="apellido_materno" icon:trailing="a-large-small"
                    label="Apellido Materno" description:trailing="Ingrese minimo 3 caracteres" />
                @error('apellido_materno') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <flux:select wire:model="estado" label="Estado" placeholder="Seleccione el estado">
                    <flux:select.option value="Activo">Activo</flux:select.option>
                    <flux:select.option value="Inactivo">Inactivo</flux:select.option>
                </flux:select>
                @error('estado') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <flux:input wire:model.live.blur="fecha_de_incorporacion" label="Fecha de Incorporación"
                    description:trailing="La fecha debe ser valida" type="date" />
                @error('fecha_de_incorporacion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <flux:input wire:model.live.blur="numero_telefonico" icon:trailing="smartphone"
                    label="Número Telefónico" description:trailing="Ingrese un numero de telefono valido" />
                @error('numero_telefonico') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <flux:input wire:model.live.blur="correo" icon:trailing="mail" label="Correo"
                    description:trailing="Ingrese un correo electronico valido" />
                @error('correo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-8">
            <flux:button type="submit" variant="primary" class="w-full">Crear Socio</flux:button>
        </div>
    </flux:card>
</form>