<?php

use Livewire\Component;
use App\Models\Sucursal;
use App\Models\Pais;
use App\Models\Estado;
use App\Models\CodigoPostal;
use App\Models\Colonia;
use App\Models\Calle;
use App\Models\Direccion;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;

new class extends Component
{
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

    public function save(){
        $this->validate();
        $this->direccion = Direccion::create([
            'id_calle' => $this->calle,
            'numero_exterior' => $this->numero_exterior,
            'numero_interior' => $this->numero_interior,
        ]);

        Sucursal::create([
            'nombre' => $this->nombre,
            'id_direccion' => $this->direccion->id_direccion,
        ]);
        $this->reset();
        $this->dispatch('sucursal-creada');
        session()->flash('status', 'Sucursal creada correctamente');
    }
};
?>

<div>
    <form wire:submit='save' class="p-6">
        <flux:card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <flux:input wire:model.live.blur="nombre" icon:trailing="a-large-small" type="text"
                        label="Nombre de la sucursal" description:trailing="Ingrese minimo 3 caracteres" />
                    @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                        label="Número exterior" description:trailing="Ingrese el número exterior" />
                    @error('numero_exterior') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <flux:input wire:model.live.blur="numero_interior" icon:trailing="hashtag" type="text"
                        label="Número interior" description:trailing="Ingrese el número interior (opcional)" />
                    @error('numero_interior') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mt-8">
                    <flux:button type="submit" variant="primary" class="w-full">Crear Sucursal</flux:button>
                </div>
            </div>
        </flux:card>
    </form>
</div>