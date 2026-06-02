<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\Cliente;

new class extends Component
{
    #[Validate('required', message: 'El nombre es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    #[Validate('regex:/^[\pL\s\-]+$/u', message: 'El nombre debe contener solo letras.')]
    public $nombre = null;

    #[Validate('required', message: 'El apellido paterno es requerido.')]
    #[Validate('min:3', message: 'El apellido paterno debe tener al menos 3 caracteres.')]
    #[Validate('regex:/^[\pL\s\-]+$/u', message: 'El apellido paterno debe contener solo letras.')]
    public $apellido_paterno = null;

    #[Validate('nullable')]
    #[Validate('min:3', message: 'El apellido materno debe tener al menos 3 caracteres.')]
    #[Validate('regex:/^[\pL\s\-]+$/u', message: 'El apellido materno debe contener solo letras.')]
    public $apellido_materno = null;

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
            && filled($this->apellido_paterno)
            && $this->getErrorBag()->isEmpty();
    }

    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset(['nombre', 'apellido_paterno', 'apellido_materno']);
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        Cliente::create([
            'nombre'           => $this->nombre,
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
                <div x-on:blur.capture="$wire.touchField('nombre')">
                    <flux:input
                        wire:model.live.blur="nombre"
                        icon:trailing="a-large-small"
                        type="text"
                        x-on:keydown="!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test($event.key)
                            && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                            && $event.preventDefault()"
                        description:trailing="Ingrese minimo 3 caracteres" />
                </div>
                <flux:error name="nombre" />
            </flux:field>

            <flux:field>
                <flux:label badge="Obligatorio">Apellido Paterno</flux:label>
                <div x-on:blur.capture="$wire.touchField('apellido_paterno')">
                    <flux:input
                        wire:model.live.blur="apellido_paterno"
                        icon:trailing="a-large-small"
                        type="text"
                        x-on:keydown="!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test($event.key)
                            && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                            && $event.preventDefault()"
                        description:trailing="Ingrese minimo 3 caracteres" />
                </div>
                <flux:error name="apellido_paterno" />
            </flux:field>

            <flux:field>
                <flux:label badge="Opcional">Apellido Materno</flux:label>
                <div x-on:blur.capture="$wire.touchField('apellido_materno')">
                    <flux:input
                        wire:model.live.blur="apellido_materno"
                        icon:trailing="a-large-small"
                        type="text"
                        x-on:keydown="!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test($event.key)
                            && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                            && $event.preventDefault()"
                        description:trailing="Ingrese minimo 3 caracteres" />
                </div>
                <flux:error name="apellido_materno" />
            </flux:field>

        </div>

        <div class="mt-8">
            <flux:button
                type="submit"
                variant="primary"
                class="w-full bg-azul_rebajado! text-azul_menu!
                       hover:bg-azul_menu! hover:text-white! hover:-translate-y-1/4
                       transition delay-130 duration-300 ease-in-out cursor-pointer border-none!"
                :disabled="!$this->formularioListo">
                Crear Cliente
            </flux:button>
        </div>
    </flux:card>
</form>