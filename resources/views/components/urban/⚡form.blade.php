<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Urban;
use App\Models\Socio;
use App\Models\Asiento;
use Livewire\Attributes\Computed;

new class extends Component {

    #[Validate('required',  message: 'El código de la urban es requerido.')]
    #[Validate('unique:urban,codigo_urban', message: 'El código de la urban ya existe.')]
    #[Validate('min:3',     message: 'El código debe tener al menos 3 caracteres.')]
    #[Validate('max:10',    message: 'El código no puede tener más de 10 caracteres.')]
    #[Validate('regex:/^[A-Za-z0-9\-]+$/', message: 'El código solo puede contener letras, números y guiones.')]
    public $codigo_urban;

    #[Validate('required', message: 'El número de asientos es requerido.')]
    #[Validate('integer',  message: 'El número de asientos debe ser un número entero.')]
    #[Validate('min:5',    message: 'Debe tener al menos 5 asientos.')]
    #[Validate('max:60',   message: 'No puede tener más de 60 asientos.')]
    public $numero_asientos;

    #[Validate('required',              message: 'El socio es requerido.')]
    #[Validate('exists:socio,id_socio', message: 'El socio seleccionado no existe.')]
    public $id_socio = '';

    #[Validate('required', message: 'La placa es requerida.')]
    #[Validate('unique:urban,placa', message: 'La placa ya está registrada.')]
    #[Validate('min:6',    message: 'La placa debe tener al menos 6 caracteres.')]
    #[Validate('max:10',   message: 'La placa no puede tener más de 10 caracteres.')]
    #[Validate(
        'regex:/^([A-Z]{3}-\d{2}-\d{2}|[A-Z]{3}-\d{3}-[A-Z]|\d{2}-[A-Z]{3}-\d{2})$/',
        message: 'Formato inválido. Ejemplos válidos: THA-12-34, THB-423-C, 01-MNA-01'
    )]
    public $placa;

    public function updated($property)
    {
        $this->validateOnly($property);
    }

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
            {{-- Código de la Urban --}}
            <flux:field>
                <flux:label badge="Obligatorio">Código de la Urban</flux:label>
                <flux:input wire:model.live.blur="codigo_urban"
                    type="text"
                    placeholder="Ej: URB001"
                    icon-trailing="a-large-small" />
                <flux:description>Alfanumérico, de 3 a 10 caracteres</flux:description>
                <flux:error name="codigo_urban" />
            </flux:field>

            {{-- Número de Asientos --}}
            <flux:field>
                <flux:label badge="Obligatorio">Número de Asientos</flux:label>
                <flux:select wire:model.live="numero_asientos" placeholder="Seleccione la cantidad de asientos">
                    <flux:select.option value="10">10 asientos</flux:select.option>
                    <flux:select.option value="15">15 asientos</flux:select.option>
                    <flux:select.option value="20">20 asientos</flux:select.option>
                </flux:select>
                <flux:description>Capacidad del vehículo: 10, 15 o 20 asientos</flux:description>
                <flux:error name="numero_asientos" />
            </flux:field>

            {{-- Socio --}}
            <flux:field>
                <flux:label badge="Obligatorio">Socio</flux:label>
                <flux:select wire:model="id_socio" placeholder="Seleccione el socio responsable"
                    searchable>
                    @foreach ($this->socios as $socio)
                        <flux:select.option value="{{ $socio->id_socio }}">
                            {{ $socio->nombre }} {{ $socio->apellido_paterno }} {{ $socio->apellido_materno }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:description>Socio propietario del vehículo</flux:description>
                <flux:error name="id_socio" />
            </flux:field>

            {{-- Placa --}}
            <flux:field>
                <flux:label badge="Obligatorio">Placa</flux:label>
                <flux:input wire:model.live.blur="placa"
                    type="text"
                    placeholder="Ej: ABC-1234"
                    icon-trailing="a-large-small" />
                <flux:description>Solo mayúsculas, números y guiones (6-10 caracteres)</flux:description>
                <flux:error name="placa" />
            </flux:field>
        </div>
        <div class="mt-8">
            <flux:button type="submit" variant="primary" class="w-full">Crear Urban</flux:button>
        </div>
    </flux:card>
</form>