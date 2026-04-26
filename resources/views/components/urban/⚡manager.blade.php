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
    #[Validate('required', message: 'El codigo de la urban es requerido.')]
    #[Validate('min:3', message: 'El codigo de la urban debe tener al menos 3 caracteres.')]
    public $codigo_urban;

    #[Validate('required', message: 'El numero de asientos es requerido.')]
    #[Validate('numeric', message: 'El numero de asientos debe ser un valor numerico.')]
    #[Validate('min:5', message: 'El numero de asientos debe tener al menos 5 asientos.')]
    #[Validate('regex:/^[0-9]+$/', message: 'El numero de asientos debe ser un valor numerico positivo.')]
    public $numero_asientos;

    #[Validate('required', message: 'El socio es requerido.')]
    public $id_socio = '';

    #[Validate('required', message: 'La placa es requerida.')]
    #[Validate('min:3', message: 'La placa debe tener al menos 3 caracteres.')]
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

    public function update()
    {
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