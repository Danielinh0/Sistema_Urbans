<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Urban;
use App\Models\Socio;
use Livewire\Attributes\Computed;

new class extends Component {

    #[Validate('required', message: 'El codigo de la urban es requerido.')]
    #[Validate('min:3', message: 'El codigo de la urban debe tener al menos 3 caracteres.')]
    public $codigo_urban;

    #[Validate('required', message: 'El numero de asientos es requerido.')]
    #[Validate('numeric', message: 'El numero de asientos debe ser un valor numerico.')]
    #[Validate('min:1', message: 'El numero de asientos debe tener al menos 1 asiento.')]
    public $numero_asientos;

    #[Validate('required', message: 'El socio es requerido.')]
    public $id_socio;

    #[Validate('required', message: 'La placa es requerida.')]
    #[Validate('min:3', message: 'La placa debe tener al menos 3 caracteres.')]
    public $placa;

    public function save()
    {
        $this->validate();
        Urban::create([
            'codigo_urban' => $this->codigo_urban,
            'numero_asientos' => $this->numero_asientos,
            'id_socio' => $this->id_socio,
            'placa' => $this->placa,
        ]);
        $this->reset();
        $this->dispatch('urban-creada');
        session()->flash('status', 'Urban creada correctamente.');
    }

    #[Computed]
    public function socios()
    {
        return Socio::orderBy('nombre')->get();
    }
};
?>

<form wire:submit="save" class="p-6">
    <flux:card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mt-6">
            <div>
                <flux:input wire:model.live.blur="codigo_urban" icon:trailing="a-large-small" type="text"
                    label="Código de la Urban" description:trailing="Ingrese minimo 3 caracteres" />
                @error('codigo_urban') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <flux:input wire:model.live.blur="numero_asientos" icon:trailing="a-large-small" type="number"
                    label="Número de Asientos" description:trailing="Ingrese el numero de asientos" />
                @error('numero_asientos') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <flux:select wire:model="id_socio" label="Socio" placeholder="Seleccione el socio" searchable>
                    @foreach ($this->socios as $socio)
                        <flux:select.option value="{{ $socio->id_socio }}">
                            {{ $socio->nombre }} {{ $socio->apellido_paterno }} {{ $socio->apellido_materno }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                @error('id_socio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <flux:input wire:model.live.blur="placa" icon:trailing="a-large-small" type="text" label="Placa"
                    description:trailing="Ingrese la placa" />
                @error('placa') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mt-8">
            <flux:button type="submit" variant="primary" class="w-full">Crear Urban</flux:button>
        </div>
    </flux:card>
</form>