<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Pais;
use App\Models\Estado;
use App\Models\CodigoPostal;
use App\Models\Colonia;
use App\Models\Calle;
use App\Models\Direccion;
use App\Models\Sucursal;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Hash;

new class extends Component
{
    #[Validate('required', message: 'El nombre es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    #[Validate('regex:/^[\pL\s\-]+$/u', message: 'El nombre solo debe contener letras.')]
    public $name;

    #[Validate('required', message: 'El apellido paterno es requerido.')]
    #[Validate('min:3', message: 'El apellido paterno debe tener al menos 3 caracteres.')]
    #[Validate('regex:/^[\pL\s\-]+$/u', message: 'El apellido paterno solo debe contener letras.')]
    public $apellido_paterno = null;

    #[Validate('nullable')]
    #[Validate('regex:/^[\pL\s\-]+$/u', message: 'El apellido materno solo debe contener letras.')]
    public $apellido_materno = null;

    #[Validate('required', message: 'El email es requerido.')]
    #[Validate('email', message: 'El email debe ser una dirección de correo válida.')]
    #[Validate('unique:users,email', message: 'Este correo ya está registrado.')]
    public $email;

    #[Validate('required', message: 'La sucursal es requerida.')]
    public $id_sucursal = null;

    #[Validate('required', message: 'El país es requerido.')]
    public $pais = null;

    #[Validate('required', message: 'El estado es requerido.')]
    public $estado = null;

    #[Validate('required', message: 'El código postal es requerido.')]
    public $codigoPostal = null;

    #[Validate('required', message: 'La colonia es requerida.')]
    public $colonia = null;

    #[Validate('required', message: 'La calle es requerida.')]
    public $calle = null;

    #[Validate('required', message: 'El número exterior es requerido.')]
    #[Validate('numeric', message: 'El número exterior debe ser un valor numérico.')]
    public $numero_exterior = null;

    #[Validate('nullable')]
    #[Validate('numeric', message: 'El número interior debe ser un valor numérico.')]
    public $numero_interior = null;

    public $direccion = null;

    #[Validate('required', message: 'La contraseña es requerida.')]
    #[Validate('min:8', message: 'La contraseña debe tener al menos 8 caracteres.')]
    public $password;

    #[Validate('required', message: 'La confirmación de contraseña es requerida.')]
    #[Validate('min:8', message: 'La confirmación debe tener al menos 8 caracteres.')]
    #[Validate('same:password', message: 'Las contraseñas no coinciden.')]
    public $password_confirmation;

    #[Validate('required', message: 'El rol es requerido.')]
    public $roles = null;

    #[Computed]
    public function paises(){
        return Pais::orderBy('nombre')->get();
    }

    #[Computed]
    public function estados(){
        if (!$this->pais) return collect();
        return Estado::where('id_pais', $this->pais)->orderby('nombre')->get();
    }

    #[Computed]
    public function codigos(){
        if (!$this->estado) return collect();
        return CodigoPostal::where('id_estado', $this->estado)->orderby('numero')->get();
    }

    #[Computed]
    public function colonias(){
        if (!$this->codigoPostal) return collect();
        return Colonia::where('id_cp', $this->codigoPostal)->orderby('nombre')->get();
    }

    #[Computed]
    public function calles(){
        if (!$this->colonia) return collect();
        return Calle::where('id_colonia', $this->colonia)->orderby('nombre')->get();
    }

    #[Computed]
    public function sucursales(){
        return Sucursal::orderBy('nombre')->get();
    }

    #[Computed]
    public function all_roles(){
        return Role::orderBy('name')->get();
    }

    public function touchField(string $field): void
    {
        $this->validateOnly($field);
    }

    public function updated($property, $value): void
    {
        $this->validateOnly($property);

        match ($property) {
            'pais'         => $this->reset('estado', 'codigoPostal', 'colonia', 'calle'),
            'estado'       => $this->reset('codigoPostal', 'colonia', 'calle'),
            'codigoPostal' => $this->reset('colonia', 'calle'),
            'colonia'      => $this->reset('calle'),
            default        => null,
        };
    }

    #[Computed]
    public function formularioListo(): bool
    {
        return filled($this->name)
            && filled($this->apellido_paterno)
            && filled($this->email)
            && filled($this->id_sucursal)
            && filled($this->pais)
            && filled($this->estado)
            && filled($this->codigoPostal)
            && filled($this->colonia)
            && filled($this->calle)
            && filled($this->numero_exterior)
            && filled($this->password)
            && filled($this->password_confirmation)
            && filled($this->roles)
            && $this->getErrorBag()->isEmpty();
    }

    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset();
        $this->resetErrorBag();
    }

    public function save(){
        $this->validate();

        $this->direccion = Direccion::create([
            'id_calle'        => $this->calle,
            'numero_exterior' => $this->numero_exterior,
            'numero_interior' => $this->numero_interior,
        ]);

        $usuario = User::create([
            'name'             => $this->name,
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
            'email'            => $this->email,
            'id_sucursal'      => $this->id_sucursal,
            'id_direccion'     => $this->direccion->id_direccion,
            'password'         => Hash::make($this->password),
        ]);

        $usuario->syncRoles([$this->roles]);
        $this->reset();
        $this->dispatch('usuario-creado');
        session()->flash('status', 'Usuario creado correctamente');
    }
};
?>

<div>
    <form wire:submit="save" class="p-6">
        <flux:card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

                {{-- Nombre --}}
                <flux:field>
                    <flux:label badge="Obligatorio">Nombre del usuario</flux:label>
                    <div x-on:blur.capture="$wire.touchField('name')">
                        <flux:input
                            wire:model.live.blur="name"
                            icon:trailing="a-large-small"
                            type="text"
                            placeholder="Ej: Juan Carlos"
                            x-on:keydown="!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test($event.key)
                                && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                                && $event.preventDefault()" />
                    </div>
                    <flux:error name="name" />
                </flux:field>

                {{-- Apellido Paterno --}}
                <flux:field>
                    <flux:label badge="Obligatorio">Apellido Paterno</flux:label>
                    <div x-on:blur.capture="$wire.touchField('apellido_paterno')">
                        <flux:input
                            wire:model.live.blur="apellido_paterno"
                            icon:trailing="a-large-small"
                            type="text"
                            placeholder="Ej: García"
                            x-on:keydown="!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test($event.key)
                                && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                                && $event.preventDefault()" />
                    </div>
                    <flux:error name="apellido_paterno" />
                </flux:field>

                {{-- Apellido Materno --}}
                <flux:field>
                    <flux:label badge="Opcional">Apellido Materno</flux:label>
                    <div x-on:blur.capture="$wire.touchField('apellido_materno')">
                        <flux:input
                            wire:model.live.blur="apellido_materno"
                            icon:trailing="a-large-small"
                            type="text"
                            placeholder="Ej: López"
                            x-on:keydown="!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]$/.test($event.key)
                                && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                                && $event.preventDefault()" />
                    </div>
                    <flux:error name="apellido_materno" />
                </flux:field>

                {{-- Email --}}
                <flux:field>
                    <flux:label badge="Obligatorio">Correo electrónico</flux:label>
                    <div x-on:blur.capture="$wire.touchField('email')">
                        <flux:input
                            wire:model.live.blur="email"
                            icon:trailing="a-large-small"
                            type="email"
                            placeholder="Ej: juan.garcia@correo.com" />
                    </div>
                    <flux:error name="email" />
                </flux:field>

                {{-- Sucursal --}}
                <flux:field>
                    <flux:label badge="Obligatorio">Sucursal</flux:label>
                    <div x-on:blur.capture="$wire.touchField('id_sucursal')">
                        <flux:select wire:model.live="id_sucursal" placeholder="Seleccione una sucursal">
                            @foreach ($this->sucursales as $sucursal)
                                <flux:select.option value="{{ $sucursal->id_sucursal }}">
                                    {{ $sucursal->nombre }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:error name="id_sucursal" />
                </flux:field>

                {{-- País --}}
                <flux:field>
                    <flux:label badge="Obligatorio">País</flux:label>
                    <div x-on:blur.capture="$wire.touchField('pais')">
                        <flux:select wire:model.live="pais" placeholder="Seleccione un país" searchable>
                            @foreach ($this->paises as $pais2)
                                <flux:select.option value="{{ $pais2->id_pais }}">
                                    {{ $pais2->nombre }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:error name="pais" />
                </flux:field>

                {{-- Estado --}}
                <flux:field>
                    <flux:label badge="Obligatorio">Estado</flux:label>
                    <div x-on:blur.capture="$wire.touchField('estado')">
                        <flux:select wire:model.live="estado" placeholder="Seleccione un estado" searchable :disabled="!$pais">
                            @foreach ($this->estados as $estado2)
                                <flux:select.option value="{{ $estado2->id_estado }}">
                                    {{ $estado2->nombre }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:error name="estado" />
                </flux:field>

                {{-- Código Postal --}}
                <flux:field>
                    <flux:label badge="Obligatorio">Código Postal</flux:label>
                    <div x-on:blur.capture="$wire.touchField('codigoPostal')">
                        <flux:select wire:model.live="codigoPostal" placeholder="Seleccione un código postal" searchable :disabled="!$estado">
                            @foreach ($this->codigos as $codigoPostal2)
                                <flux:select.option value="{{ $codigoPostal2->id_cp }}">
                                    {{ $codigoPostal2->numero }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:error name="codigoPostal" />
                </flux:field>

                {{-- Colonia --}}
                <flux:field>
                    <flux:label badge="Obligatorio">Colonia</flux:label>
                    <div x-on:blur.capture="$wire.touchField('colonia')">
                        <flux:select wire:model.live="colonia" placeholder="Seleccione una colonia" searchable :disabled="!$codigoPostal">
                            @foreach ($this->colonias as $colonia2)
                                <flux:select.option value="{{ $colonia2->id_colonia }}">
                                    {{ $colonia2->nombre }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:error name="colonia" />
                </flux:field>

                {{-- Calle --}}
                <flux:field>
                    <flux:label badge="Obligatorio">Calle</flux:label>
                    <div x-on:blur.capture="$wire.touchField('calle')">
                        <flux:select wire:model.live="calle" placeholder="Seleccione una calle" searchable :disabled="!$colonia">
                            @foreach ($this->calles as $calle2)
                                <flux:select.option value="{{ $calle2->id_calle }}">
                                    {{ $calle2->nombre }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:error name="calle" />
                </flux:field>

                {{-- Número Exterior --}}
                <flux:field>
                    <flux:label badge="Obligatorio">Número Exterior</flux:label>
                    <div x-on:blur.capture="$wire.touchField('numero_exterior')">
                        <flux:input
                            wire:model.live.blur="numero_exterior"
                            icon:trailing="hashtag"
                            type="text"
                            inputmode="numeric"
                            placeholder="Ej: 123"
                            x-on:keydown="!/^[0-9]$/.test($event.key)
                                && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                                && $event.preventDefault()" />
                    </div>
                    <flux:error name="numero_exterior" />
                </flux:field>

                {{-- Número Interior --}}
                <flux:field>
                    <flux:label badge="Opcional">Número Interior</flux:label>
                    <div x-on:blur.capture="$wire.touchField('numero_interior')">
                        <flux:input
                            wire:model.live.blur="numero_interior"
                            icon:trailing="hashtag"
                            type="text"
                            inputmode="numeric"
                            placeholder="Ej: 4B (opcional)"
                            x-on:keydown="!/^[0-9]$/.test($event.key)
                                && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)
                                && $event.preventDefault()" />
                    </div>
                    <flux:error name="numero_interior" />
                </flux:field>

                {{-- Contraseña --}}
                <flux:field>
                    <flux:label badge="Obligatorio">Contraseña</flux:label>
                    <div x-on:blur.capture="$wire.touchField('password')">
                        <flux:input
                            wire:model.live.blur="password"
                            type="password"
                            placeholder="Mínimo 8 caracteres"
                            viewable />
                    </div>
                    <flux:error name="password" />
                </flux:field>

                {{-- Confirmar Contraseña --}}
                <flux:field>
                    <flux:label badge="Obligatorio">Confirmar Contraseña</flux:label>
                    <div x-on:blur.capture="$wire.touchField('password_confirmation')">
                        <flux:input
                            wire:model.live.blur="password_confirmation"
                            type="password"
                            placeholder="Repita la contraseña"
                            viewable />
                    </div>
                    <flux:error name="password_confirmation" />
                </flux:field>

                {{-- Rol --}}
                <flux:field>
                    <flux:label badge="Obligatorio">Rol</flux:label>
                    <div x-on:blur.capture="$wire.touchField('roles')">
                        <flux:select wire:model.live="roles" placeholder="Seleccione un rol" searchable>
                            @foreach ($this->all_roles as $rol)
                                <flux:select.option value="{{ $rol->name }}">
                                    {{ $rol->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:error name="roles" />
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
                    Crear Usuario
                </flux:button>
            </div>
        </flux:card>
    </form>
</div>