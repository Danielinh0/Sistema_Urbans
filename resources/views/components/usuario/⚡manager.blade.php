<?php


use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\Sucursal;
use App\Models\Pais;
use App\Models\Estado;
use App\Models\CodigoPostal;
use App\Models\Colonia;
use App\Models\Calle;
use App\Models\Direccion;
use App\Models\User;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Hash;

new class extends Component
{
    public ?User $usuario = null;

    #[Validate('required', message: 'El nombre es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    public $name;

    #[Validate('required', message: 'El apellido paterno es requerido.')]
    #[Validate('min:3', message: 'El apellido paterno debe tener al menos 3 caracteres.')]
    public $apellido_paterno = null;

    #[Validate('min:3', message: 'El apellido materno debe tener al menos 3 caracteres.')]
    #[Validate('nullable')]
    public $apellido_materno = null;

    #[Validate('required', message: 'El email es requerido.')]
    #[Validate('email', message: 'El email debe ser una dirección de correo válida.')]
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

    #[Validate('min:8', message: 'La contraseña debe tener al menos 8 caracteres.')]
    #[Validate('same:password_confirmation', message: 'Las contraseñas no coinciden.')]
    #[Validate('nullable')]
    public $password;

    #[Validate('min:8', message: 'La confirmación de contraseña debe tener al menos 8 caracteres.')]
    #[Validate('same:password', message: 'Las contraseñas no coinciden.')]
    #[Validate('nullable')]
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

    #[On('preparar-edicion-usuario')]
    public function prepararEdicion($id){
        $this->usuario = User::find($id);
        $this->name = $this->usuario->name;
        $this->apellido_paterno = $this->usuario->apellido_paterno;
        $this->apellido_materno = $this->usuario->apellido_materno;
        $this->email = $this->usuario->email;
        $this->id_sucursal = $this->usuario->id_sucursal;
        $this->direccion = $this->usuario->direccion;
        $this->calle = $this->direccion->calle->id_calle;
        $this->colonia = $this->direccion->calle->colonia->id_colonia;
        $this->codigoPostal = $this->direccion->calle->colonia->codigoPostal->id_cp;
        $this->estado = $this->direccion->calle->colonia->codigoPostal->estado->id_estado;
        $this->pais = $this->direccion->calle->colonia->codigoPostal->estado->pais->id_pais;
        $this->numero_exterior = $this->usuario->direccion->numero_exterior;
        $this->numero_interior = $this->usuario->direccion->numero_interior;
        $this->js("Flux.modal('modal-editar-usuario').show()");
    }

    #[On('preparar-eliminacion-usuario')]
    public function prepararEliminacion($id)
    {
        $this->usuario = User::findOrFail($id);
        $this->js("Flux.modal('modal-eliminar-usuario').show()");
    }

    public function update(){
        $this->validate();

        $this->direccion->update([
            'id_calle' => $this->calle,
            'numero_exterior' => $this->numero_exterior,
            'numero_interior' => $this->numero_interior,
        ]);

        if (!$this->password) {
            $this->usuario->update([
                'name' => $this->name,
                'apellido_paterno' => $this->apellido_paterno,
                'apellido_materno' => $this->apellido_materno,
                'email' => $this->email,
                'id_sucursal' => $this->id_sucursal,
            ]);
        }else{
            $this->usuario->update([
                'name' => $this->name,
                'apellido_paterno' => $this->apellido_paterno,
                'apellido_materno' => $this->apellido_materno,
                'email' => $this->email,
                'id_sucursal' => $this->id_sucursal,
                'password' => Hash::make($this->password),
            ]);
        }
        $this->usuario->syncRoles([$this->roles]);
        $this->js("Flux.modal('modal-editar-usuario').close()");
        $this->dispatch('usuario-actualizado');
    }

    public function delete(){
        $this->usuario->delete();

        $this->js("Flux.modal('modal-eliminar-usuario').close()");
        $this->dispatch('usuario-eliminado');
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
    <flux:modal name="modal-editar-usuario" class="w-[100%] p-10">
        @if ($this->usuario)
            <flux:heading size="lg">Editar Usuario: {{ $this->usuario->name }} {{ $this->usuario->apellido_paterno }} {{ $this->usuario->apellido_materno }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mt-6">
                <flux:input wire:model.live.blur="name" label="Nombre" />
                <flux:input wire:model.live.blur="apellido_paterno" label="Apellido Paterno" />
                <flux:input wire:model.live.blur="apellido_materno" label="Apellido Materno" />
                <flux:input wire:model.live.blur="email" label="Email" type="email" />
                <flux:select wire:model.live.blur="pais" label="País">
                    <flux:select.option value="">Seleccione un país</flux:select.option>
                    @foreach($this->paises as $pais2)
                        <flux:select.option value="{{ $pais2->id_pais }}">{{ $pais2->nombre }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live.blur="estado" label="Estado" searchable :disabled="!$this->pais">
                    <flux:select.option value="">Seleccione un estado</flux:select.option>
                    @foreach($this->estados as $estado)
                        <flux:select.option value="{{ $estado->id_estado }}">{{ $estado->nombre }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live.blur="codigoPostal" label="Código Postal" searchable :disabled="!$this->estado">
                    <flux:select.option value="">Seleccione un código postal</flux:select.option>
                    @foreach($this->codigos as $codigo)
                        <flux:select.option value="{{ $codigo->id_cp }}">{{ $codigo->numero }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live.blur="colonia" label="Colonia" searchable :disabled="!$this->codigoPostal">
                    <flux:select.option value="">Seleccione una colonia</flux:select.option>
                    @foreach($this->colonias as $colonia)
                        <flux:select.option value="{{ $colonia->id_colonia }}">{{ $colonia->nombre }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live.blur="calle" label="Calle" searchable :disabled="!$this->colonia">
                    <flux:select.option value="">Seleccione una calle</flux:select.option>
                    @foreach($this->calles as $calle)
                        <flux:select.option value="{{ $calle->id_calle }}">{{ $calle->nombre }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input wire:model.live.blur="numero_exterior" label="Número Exterior" />
                <flux:input wire:model.live.blur="numero_interior" label="Número Interior" />
                

            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mt-6">
                <flux:input wire:model.live.blur="password" type="password" label="Contraseña" description:trailing="Ingrese la contraseña nueva (en caso de no querer cambiar la contraseña actual, deje los campos vacios)" viewable/>
                <flux:input wire:model.live.blur="password_confirmation" type="password" label="Confirmar Contraseña" description:trailing="Confirme la contraseña" viewable/>
            </div>
            <div class="mt-8">
                <flux:button wire:click="update" variant="primary" class="w-full">Guardar Cambios</flux:button>
            </div>
        @endif
    </flux:modal>
    <flux:modal class="space-y-6" name="modal-eliminar-usuario" class="min-w-[22rem]">
        @if($this->usuario)
            <div>
                <flux:heading size="lg">Eliminar Usuario</flux:heading>
                <flux:text>
                    ¿Estás seguro de que deseas eliminar el usuario <b>{{ $this->usuario->name }}</b>?
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