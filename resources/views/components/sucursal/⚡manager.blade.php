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
use Livewire\Attributes\Computed;

new class extends Component
{
    public ?Sucursal $sucursal = null;

    #[Validate('required', message: 'El nombre es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    public $nombre;

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

    #[On('preparar-edicion-sucursal')]
    public function prepararEdicion($id)
    {
        $this->sucursal = Sucursal::findOrFail($id);
        $this->nombre = $this->sucursal->nombre;
        $this->direccion = $this->sucursal->direccion;
        $this->calle = $this->direccion->calle->id_calle;
        $this->colonia = $this->direccion->calle->colonia->id_colonia;
        $this->codigoPostal = $this->direccion->calle->colonia->codigoPostal->id_cp;
        $this->estado = $this->direccion->calle->colonia->codigoPostal->estado->id_estado;
        $this->pais = $this->direccion->calle->colonia->codigoPostal->estado->pais->id_pais;
        $this->numero_exterior = $this->sucursal->direccion->numero_exterior;
        $this->numero_interior = $this->sucursal->direccion->numero_interior;
        $this->js("Flux.modal('modal-editar-sucursal').show()");
    }

    #[On('preparar-eliminacion-sucursal')]
    public function prepararEliminacion($id)
    {
        $this->sucursal = Sucursal::findOrFail($id);
        $this->js("Flux.modal('modal-eliminar-sucursal').show()");
    }

    public function update()
    {
        $this->validate();

        $this->direccion->update([
            'id_calle' => $this->calle,
            'numero_exterior' => $this->numero_exterior,
            'numero_interior' => $this->numero_interior,
        ]);

        $this->sucursal->update([
            'nombre' => $this->nombre,
        ]);

        $this->js("Flux.modal('modal-editar-sucursal').close()");
        $this->dispatch('sucursal-actualizada');
    }

    public function delete()
    {
        $this->sucursal->delete();

        $this->js("Flux.modal('modal-eliminar-sucursal').close()");
        $this->dispatch('sucursal-eliminada');
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
    <flux:modal name="modal-editar-sucursal" class="w-[60%] p-10">
        @if($sucursal)
            <flux:heading size="lg">Editar Sucursal: {{ $sucursal->nombre }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mt-6">
                <flux:input wire:model.live.blur="nombre" label="Nombre" />
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

            <div class="mt-8">
                <flux:button wire:click="update" variant="primary" class="w-full">Guardar Cambios</flux:button>
            </div>
        @endif
    </flux:modal>
    <flux:modal name="modal-eliminar-sucursal" class="min-w-[22rem]">
        @if($sucursal)
            <div class="space-y-6">
                <flux:heading size="lg">Eliminar Sucursal</flux:heading>
                <flux:text>
                    ¿Estás seguro de que deseas eliminar la sucursal <b>{{ $sucursal->nombre }}</b>?
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