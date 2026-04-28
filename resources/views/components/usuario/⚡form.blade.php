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

    #[Validate('regex:/^[\pL\s\-]+$/u', message: 'El apellido materno solo debe contener letras.')]
    #[Validate('nullable')]
    public $apellido_materno = null;

    #[Validate('required', message: 'El email es requerido.')]
    #[Validate('email', message: 'El email debe ser una dirección de correo válida.')]
    #[Validate('unique:users,email', message: 'Este correo ya está registrado.')]
    public $email;
    
    #[Validate('required', message: 'la sucursal es requerida.')]
    public $id_sucursal;

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

    #[Validate('numeric', message: 'El número interior debe ser un valor numérico.')]
    #[Validate('nullable')]
    public $numero_interior = null;

    public $direccion = null;

    #[Validate('required', message: 'La contraseña es requerida.')]
    #[Validate('min:8', message: 'La contraseña debe tener al menos 8 caracteres.')]
    public $password;

    #[Validate('required', message: 'La confirmación de contraseña es requerida.')]
    #[Validate('min:8', message: 'La confirmación de contraseña debe tener al menos 8 caracteres.')]
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
        if (!$this->pais) {
            return collect();
        }
        return Estado::where('id_pais', $this->pais)->orderby('nombre')->get();
    }

    #[Computed]
    public function codigos(){
        if (!$this->estado) {
            return collect();
        }
        return CodigoPostal::where('id_estado', $this->estado)->orderby('numero')->get();
    }

    #[Computed]
    public function colonias(){
        if (!$this->codigoPostal){
            return collect();
        }
        return Colonia::where('id_cp', $this->codigoPostal)->orderby('nombre')->get();
    }

    #[Computed]
    public function calles(){
        if (!$this->colonia){
            return collect();
        }
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

    public function save(){
        $this->validate();
        $this->direccion = Direccion::create([
            'id_calle' => $this->calle,
            'numero_exterior' => $this->numero_exterior,
            'numero_interior' => $this->numero_interior,
        ]);

        $usuario = User::create([
            'name' => $this->name,
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
            'email' => $this->email,
            'id_sucursal' => $this->id_sucursal,
            'id_direccion' => $this->direccion->id_direccion,
            'password' => Hash::make($this->password),
        ]);

        $usuario->syncRoles([$this->roles]);
        $this->reset();
        $this->dispatch('usuario-creado');
        session()->flash('status', 'Usuario creado correctamente');
    }

    public function updated($property, $value): void
    {
        match ($property) {
            'pais' => $this->reset('estado', 'codigoPostal', 'colonia', 'calle'),
            'estado' => $this->reset('codigoPostal', 'colonia', 'calle'),
            'codigoPostal' => $this->reset('colonia', 'calle'),
            'colonia' => $this->reset('calle'),
            default => null,
        };
    }

};
?>

<div>
    <form wire:submit='save' class="p-6">
        <flux:card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <flux:input wire:model.live.blur="name" icon:trailing="a-large-small" type="text"
                        label="Nombre del usuario" placeholder="Ej: Juan Carlos" />
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <flux:input wire:model.live.blur="apellido_paterno" icon:trailing="a-large-small" type="text"
                        label="Apellido Paterno" placeholder="Ej: García" />
                    @error('apellido_paterno') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <flux:input wire:model.live.blur="apellido_materno" icon:trailing="a-large-small" type="text"
                        label="Apellido Materno" placeholder="Ej: López" />
                    @error('apellido_materno') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <flux:input wire:model.live.blur="email" icon:trailing="a-large-small" type="email"
                        label="Correo electrónico" placeholder="Ej: juan.garcia@correo.com" />
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <flux:select wire:model.live.blur="id_sucursal" label="Sucursal">
                        <flux:select.option value="">Seleccione una sucursal</flux:select>
                        @foreach ($this->sucursales as $sucursal)
                            <flux:select.option value="{{ $sucursal->id_sucursal }}">
                                {{ $sucursal->nombre }}
                            </flux:select.option>
                        @endforeach
                        @error('id_sucursal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </flux:select>
                </div>
                <div>
                    <flux:select wire:model.live.blur="pais" label="Pais" searchable>
                        <flux:select.option value="">Seleccione un pais</flux:select.option>
                        @foreach ($this->paises as $pais2)
                            <flux:select.option value="{{ $pais2->id_pais }}">
                                {{ $pais2->nombre }}
                            </flux:select.option>
                        @endforeach
                        @error('pais') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </flux:select>
                </div>
                <div>
                    <flux:select wire:model.live.blur="estado" label="Estado" searchable :disabled="!$pais">
                            <flux:select.option value="">Seleccione un estado</flux:select.option>
                            @foreach ($this->estados as $estado2)
                                <flux:select.option value="{{ $estado2->id_estado }}">
                                    {{ $estado2->nombre }}
                                </flux:select.option>
                            @endforeach
                            @error('estado') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        
                    </flux:select>
                </div>
                <div>
                    <flux:select wire:model.live.blur="codigoPostal" label="Codigo postal" searchable :disabled="!$estado">
                            <flux:select.option value="">Seleccione un codigo postal</flux:select.option>
                            @foreach ($this->codigos as $codigoPostal2)
                                <flux:select.option value="{{ $codigoPostal2->id_cp }}">
                                    {{ $codigoPostal2->numero }}
                                </flux:select.option>
                            @endforeach
                            @error('codigoPostal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        
                    </flux:select>
                </div>
                <div>
                    <flux:select wire:model.live.blur="colonia" label="Colonia" searchable :disabled="!$codigoPostal">
                            <flux:select.option value="">Seleccione una colonia</flux:select.option>
                            @foreach ($this->colonias as $colonia2)
                                <flux:select.option value="{{ $colonia2->id_colonia }}">
                                    {{ $colonia2->nombre }}
                                </flux:select.option>
                            @endforeach
                            @error('colonia') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        
                    </flux:select>
                </div>
                <div>
                    <flux:select wire:model.live.blur="calle" label="Calle" searchable :disabled="!$colonia">
                            <flux:select.option value="">Seleccione una calle</flux:select.option>
                            @foreach ($this->calles as $calle2)
                                <flux:select.option value="{{ $calle2->id_calle }}">
                                    {{ $calle2->nombre }}
                                </flux:select.option>
                            @endforeach
                            @error('calle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        
                    </flux:select>
                </div>
                <div>
                    <flux:input wire:model.live.blur="numero_exterior" icon:trailing="hashtag" type="text"
                        label="Número exterior" placeholder="Ej: 123" />
                    @error('numero_exterior') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <flux:input wire:model.live.blur="numero_interior" icon:trailing="hashtag" type="text"
                        label="Número interior" placeholder="Ej: 4B (opcional)" />
                    @error('numero_interior') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <flux:input wire:model.live.blur="password" type="password"
                        label="Contraseña" placeholder="Mínimo 8 caracteres" viewable/>
                    @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <flux:input wire:model.live.blur="password_confirmation" type="password"
                        label="Confirmar contraseña" placeholder="Repita la contraseña" viewable/>
                    @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <flux:select wire:model.live.blur="roles" label="Roles" searchable>
                        <flux:select.option value="">Seleccione un rol</flux:select.option>
                        @foreach ($this->all_roles as $rol)
                            <flux:select.option value="{{ $rol->name }}">
                                {{ $rol->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="mt-8">
                    <flux:button type="submit" variant="primary" class="w-full">Crear usuario</flux:button>
                </div>
            </div>
            
        </flux:card>
    </form>
</div>