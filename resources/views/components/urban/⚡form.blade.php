<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Urban;
use App\Models\Socio;
use App\Models\Asiento;
use Livewire\Attributes\Computed;

new class extends Component {

    #[Validate('required', message: 'El codigo de la urban es requerido.')]
    #[Validate('min:3', message: 'El codigo de la urban debe tener al menos 3 caracteres.')]
    public $codigo_urban;

    #[Validate('required', message: 'El numero de asientos es requerido.')]
    #[Validate('numeric', message: 'El numero de asientos debe ser un valor numerico.')]
    #[Validate('regex:/^[0-9]+$/', message: 'El numero de asientos debe ser un valor numerico positivo.')]
    #[Validate('min:5', message: 'El numero de asientos debe tener al menos 5 asientos.')]
    public $numero_asientos;

    #[Validate('required', message: 'El socio es requerido.')]
    public $id_socio = '';

    #[Validate('required', message: 'La placa es requerida.')]
    #[Validate('min:3', message: 'La placa debe tener al menos 3 caracteres.')]
    public $placa;

    public function save()
    {
        $this->validate();
        $urban = Urban::create([
            'codigo_urban' => $this->codigo_urban,
            'numero_asientos' => $this->numero_asientos,
            'id_socio' => $this->id_socio,
            'placa' => $this->placa,
        ]);

        for ($i = 0; $i < $this->numero_asientos; $i++) {
            Asiento::create([
                'id_urban' => $urban->id_urban,
                'nombre' => $this->codigo_urban . '-' . '0' . ($i + 1),
                'estado' => 'Libre',
            ]);
        }

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
            <flux:field>
                <flux:label badge="Obligatorio">Código de la Urban</flux:label>
                <flux:input wire:model.live.blur="codigo_urban" icon:trailing="a-large-small" type="text"
                    description:trailing="Ingrese minimo 3 caracteres" />
                <flux:error name="codigo_urban" />
            </flux:field>
            <flux:field>
                <flux:label badge="Obligatorio">Número de Asientos</flux:label>
                <flux:input wire:model.live.blur="numero_asientos" icon:trailing="a-large-small" type="number"
                    description:trailing="Ingrese el numero de asientos" />
                <flux:error name="numero_asientos" />
            </flux:field>
            <flux:field>
                <flux:label badge="Obligatorio">Socio</flux:label>
                <flux:select wire:model="id_socio" placeholder="Seleccione el socio"
                    description:trailing="Seleccione el socio" searchable>
                    @foreach ($this->socios as $socio)
                        <flux:select.option value="{{ $socio->id_socio }}">
                            {{ $socio->nombre }} {{ $socio->apellido_paterno }} {{ $socio->apellido_materno }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="id_socio" />
            </flux:field>
            <flux:field>
                <flux:label badge="Obligatorio">Placa</flux:label>
                <flux:input wire:model.live.blur="placa" icon:trailing="a-large-small" type="text"
                    description:trailing="Ingrese la placa" />
                <flux:error name="placa" />
            </flux:field>
        </div>
        <div class="mt-8">
            <flux:button type="submit" variant="primary" class="w-full">Crear Urban</flux:button>
        </div>
    </flux:card>
</form>