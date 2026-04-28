<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\Urban;
use App\Models\Socio;
use App\Models\Asiento;
use App\Models\Corrida;
use Livewire\Attributes\Computed;

new class extends Component {
    public ?Urban $urban = null;

    // Reglas de validación idénticas al formulario de creación
    #[Validate('required',  message: 'El código de la urban es requerido.')]
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
    #[Validate('min:6',    message: 'La placa debe tener al menos 6 caracteres.')]
    #[Validate('max:10',   message: 'La placa no puede tener más de 10 caracteres.')]
    #[Validate('regex:/^([A-Z]{3}-\d{2}-\d{2}|[A-Z]{3}-\d{3}-[A-Z]|\d{2}-[A-Z]{3}-\d{2})$/', message: 'Formato inválido. Ejemplos válidos: THA-12-34, THB-423-C, 01-MNA-01')]
    public $placa;

    #[On('preparar-edicion-urban')]
    public function prepararEdicion($id)
    {
        $this->resetErrorBag();
        session()->forget('error');

        $this->urban = Urban::findOrFail($id);
        $this->codigo_urban = $this->urban->codigo_urban;
        $this->numero_asientos = $this->urban->numero_asientos;
        $this->id_socio = $this->urban->id_socio;
        $this->placa = $this->urban->placa;

        $this->js("Flux.modal('modal-editar-urban').show()");
    }

    #[On('preparar-eliminacion-urban')]
    public function prepararEliminacion($id)
    {
        $this->resetErrorBag();
        session()->forget('error');

        $this->urban = Urban::findOrFail($id);
        $this->js("Flux.modal('modal-eliminar-urban').show()");
    }

    public function updated($property)
    {
        if ($property === 'codigo_urban') {
            $this->validate([
                'codigo_urban' => [
                    'required',
                    'min:3',
                    'max:10',
                    'regex:/^[A-Za-z0-9\-]+$/',
                    \Illuminate\Validation\Rule::unique('urban', 'codigo_urban')->ignore($this->urban?->id_urban, 'id_urban')
                ]
            ], [
                'codigo_urban.unique' => 'El código de la urban ya existe.',
            ]);
        } elseif ($property === 'placa') {
            $this->validate([
                'placa' => [
                    'required',
                    'min:6',
                    'max:10',
                    'regex:/^([A-Z]{3}-\d{2}-\d{2}|[A-Z]{3}-\d{3}-[A-Z]|\d{2}-[A-Z]{3}-\d{2})$/',
                    \Illuminate\Validation\Rule::unique('urban', 'placa')->ignore($this->urban?->id_urban, 'id_urban')
                ]
            ], [
                'placa.unique' => 'La placa ya está registrada.',
                'placa.regex' => 'Formato inválido. Ejemplos válidos: THA-12-34, THB-423-C, 01-MNA-01'
            ]);
        } else {
            $this->validateOnly($property);
        }
    }

    public function update()
    {
        // Validar unicidad ignorando a esta urban
        $this->validate([
            'codigo_urban' => [
                'required',
                \Illuminate\Validation\Rule::unique('urban', 'codigo_urban')->ignore($this->urban->id_urban, 'id_urban')
            ],
            'placa' => [
                'required',
                \Illuminate\Validation\Rule::unique('urban', 'placa')->ignore($this->urban->id_urban, 'id_urban')
            ]
        ], [
            'codigo_urban.unique' => 'El código de la urban ya existe.',
            'placa.unique' => 'La placa ya está registrada.'
        ]);

        $this->validate();

        if ($this->tieneViajesPendientes()) {
            session()->flash('error', 'No puedes editar este vehículo porque tiene corridas pendientes o  en curso.');
            return;
        }

        $this->urban->update([
            'codigo_urban' => $this->codigo_urban,
            'numero_asientos' => $this->numero_asientos,
            'id_socio' => $this->id_socio,
            'placa' => $this->placa,
        ]);

        // 1. Buscamos TODOS los asientos (incluyendo los "borrados" anteriormente)
        $todosLosAsientos = Asiento::withTrashed()
            ->where('id_urban', $this->urban->id_urban)
            ->get();

        $cantidadActual = $todosLosAsientos->count();

        if ($this->numero_asientos > $cantidadActual) {
            // Si necesito 15 y solo tengo 10 en total (vivos o muertos)...

            // Primero: Restauramos todos los que estaban borrados
            Asiento::withTrashed()
                ->where('id_urban', $this->urban->id_urban)
                ->restore();

            // Segundo: Creamos solo los que faltan para llegar a la nueva meta
            for ($i = $cantidadActual; $i < $this->numero_asientos; $i++) {
                Asiento::create([
                    'id_urban' => $this->urban->id_urban,
                    'nombre' => $this->codigo_urban . '-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                    'estado' => 'Libre',
                ]);
            }
        } else {
            // Si necesito 10 y tengo 15...

            // Primero: Restauramos todos (para limpiar el estado)
            Asiento::withTrashed()->where('id_urban', $this->urban->id_urban)->restore();

            // Segundo: Borramos (SoftDelete) solo los que sobran al final
            $sobrantes = $cantidadActual - $this->numero_asientos;
            Asiento::where('id_urban', $this->urban->id_urban)
                ->orderBy('id_asiento', 'desc')
                ->limit($sobrantes)
                ->delete();
        }

        $this->js("Flux.modal('modal-editar-urban').close()");
        $this->dispatch('urban-creada');
    }

    public function delete()
    {
        if ($this->tieneViajesPendientes()) {
            session()->flash('error', 'No puedes eliminar este vehículo porque tiene corridas pendientes o en curso.');
            return;
        }

        $this->urban->delete();

        // Opcional: Solo borra asientos si realmente ya no los necesitas para historial
        Asiento::where('id_urban', $this->urban->id_urban)->delete();

        $this->js("Flux.modal('modal-eliminar-urban').close()");
        $this->dispatch('urban-eliminada');
    }

    #[Computed]
    public function socios()
    {
        return Socio::orderBy('nombre')->get();
    }

    public function tieneViajesPendientes()
    {
        if (!$this->urban)
            return false;

        return Urban::where('id_urban', $this->urban->id_urban)
            ->conViajesPendientes()
            ->exists();
    }
};
?>

<div>
    <flux:modal name="modal-editar-urban" class="w-[60%] p-10">
        @if($urban)
            <flux:heading size="lg" class="mb-6">Editar Urban: {{ $urban->codigo_urban }}</flux:heading>
            @if (session()->has('error'))
                <div class="p-3 mb-4 text-sm text-red-600 bg-red-50 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
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
                <flux:button wire:click="update" variant="primary" class="w-full">Guardar Cambios</flux:button>
            </div>
        @endif
    </flux:modal>

    <flux:modal name="modal-eliminar-urban" class="min-w-[22rem]">
        @if($urban)
            <div class="space-y-6">
                <flux:heading size="lg">Eliminar Urban</flux:heading>
                @if (session()->has('error'))
                    <div class="p-3 mb-4 text-sm text-red-600 bg-red-50 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif
                <flux:text>
                    ¿Estás seguro de que deseas eliminar la urban <b>{{ $urban->codigo_urban }}</b>?
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