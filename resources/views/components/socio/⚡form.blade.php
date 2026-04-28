<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Socio;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

new class extends Component {
    #[Validate('required', message: 'El nombre es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    #[Validate('max:18', message: 'El nombre debe tener como maximo 18 caracteres.')]
    #[Validate('regex:/^[\pL\s\-]+$/u', message: 'El nombre debe contener solo letras.')]
    public $nombre;

    #[Validate('nullable')]
    #[Validate('min:3', message: 'El apellido paterno debe tener al menos 3 caracteres.')]
    #[Validate('max:12', message: 'El apellido paterno debe tener como maximo 12 caracteres.')]
    #[Validate('regex:/^[\pL\s\-]+$/u', message: 'El apellido paterno debe contener solo letras.')]
    public $apellido_paterno;

    #[Validate('nullable')]
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
    #[Validate('email', message: 'El correo debe ser un correo válido.')]
    public $correo;

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function touchField(string $field): void
    {
        $this->validateOnly($field);
    }

    #[Computed]
    public function formularioListo(): bool
    {
        return filled($this->nombre)
            && filled($this->estado)
            && filled($this->fecha_de_incorporacion)
            && filled($this->numero_telefonico)
            && $this->getErrorBag()->isEmpty();
    }

    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset();
        $this->resetErrorBag();
    }

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

            {{-- Solo letras --}}
            <flux:field>
                <flux:label badge="Obligatorio">Nombre(s)</flux:label>
                <div x-on:blur.capture="$wire.touchField('nombre')">
                    <flux:input
                        wire:model.live.blur="nombre"
                        x-on:keydown="!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test($event.key)
                            && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                            && $event.preventDefault()"
                        icon:trailing="a-large-small"
                        type="text"
                        description:trailing="Ingrese minimo 3 caracteres" />
                </div>
                <flux:error name="nombre" />
            </flux:field>

            <flux:field>
                <flux:label badge="Opcional">Apellido Paterno</flux:label>
                <div x-on:blur.capture="$wire.touchField('apellido_paterno')">
                    <flux:input
                        wire:model.live.blur="apellido_paterno"
                        x-on:keydown="!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test($event.key)
                            && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                            && $event.preventDefault()"
                        icon:trailing="a-large-small"
                        description:trailing="Ingrese minimo 3 caracteres" />
                </div>
                <flux:error name="apellido_paterno" />
            </flux:field>

            <flux:field>
                <flux:label badge="Opcional">Apellido Materno</flux:label>
                <div x-on:blur.capture="$wire.touchField('apellido_materno')">
                    <flux:input
                        wire:model.live.blur="apellido_materno"
                        x-on:keydown="!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test($event.key)
                            && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                            && $event.preventDefault()"
                        icon:trailing="a-large-small"
                        description:trailing="Ingrese minimo 3 caracteres" />
                </div>
                <flux:error name="apellido_materno" />
            </flux:field>

            <flux:field>
                <flux:label badge="Obligatorio">Estado</flux:label>
                <div x-on:blur.capture="$wire.touchField('estado')"> {{-- 👈 --}}
                    <flux:select wire:model.live="estado" placeholder="Seleccione el estado">
                        <flux:select.option value="Activo">Activo</flux:select.option>
                        <flux:select.option value="Inactivo">Inactivo</flux:select.option>
                    </flux:select>
                </div>
                <flux:error name="estado" />
            </flux:field>

            {{-- Date tampoco necesita restricción de teclas --}}
            <flux:field>
                <flux:label badge="Obligatorio">Fecha de Incorporación</flux:label>
                <div x-on:blur.capture="$wire.touchField('fecha_de_incorporacion')">
                    <flux:input
                        wire:model.live.blur="fecha_de_incorporacion"
                        type="date"
                        x-bind:min="new Date().toISOString().split('T')[0]"
                        description:trailing="La fecha debe ser valida" />
                </div>
                <flux:error name="fecha_de_incorporacion" />
            </flux:field>

            {{-- Solo números --}}
            <flux:field>
                <flux:label badge="Obligatorio">Número Telefónico</flux:label>
                <div x-on:blur.capture="$wire.touchField('numero_telefonico')">
                    <flux:input
                        wire:model.live.blur="numero_telefonico"
                        type="text"
                        inputmode="numeric"
                        x-on:keydown="!/^[0-9]$/.test($event.key)
        && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
        && $event.preventDefault()"
                        icon:trailing="smartphone"
                        description:trailing="Ingrese un numero de telefono valido" />
                </div>
                <flux:error name="numero_telefonico" />
            </flux:field>

            <flux:field>
                <flux:label badge="Opcional">Correo</flux:label>
                <div x-on:blur.capture="$wire.touchField('correo')">
                    <flux:input
                        wire:model.live.blur="correo"
                        icon:trailing="mail"
                        description:trailing="Ingrese un correo electronico valido" />
                </div>
                <flux:error name="correo" />
            </flux:field>

        </div>

        <div class="mt-8">
            <flux:button
                type="submit"
                variant="primary"
                class="w-full"
                :disabled="!$this->formularioListo">
                Crear Socio
            </flux:button>
        </div>
    </flux:card>
</form>