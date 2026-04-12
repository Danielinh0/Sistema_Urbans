<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\Urban;
use App\Models\Socio;
use App\Models\Asiento;
use Livewire\Attributes\Computed;

new class extends Component {
    public ?Urban $urban = null;

    // Reglas de validación idénticas al formulario de creación
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

    #[On('preparar-edicion-urban')]
    public function prepararEdicion($id)
    {
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
        $this->urban = Urban::findOrFail($id);
        $this->js("Flux.modal('modal-eliminar-urban').show()");
    }

    public function update()
    {
        $this->validate();

        $this->urban->update([
            'codigo_urban' => $this->codigo_urban,
            'numero_asientos' => $this->numero_asientos,
            'id_socio' => $this->id_socio,
            'placa' => $this->placa,
        ]);

        Asiento::where('id_urban', $this->urban->id_urban)->delete();

        for ($i = 0; $i < $this->numero_asientos; $i++) {
            Asiento::create([
                'id_urban' => $this->urban->id_urban,
                'nombre' => $this->codigo_urban . '-' . '0' . ($i + 1),
                'estado' => 'Libre',
            ]);
        }

        $this->js("Flux.modal('modal-editar-urban').close()");
        $this->dispatch('urban-creada');
    }

    public function delete()
    {
        $this->urban->delete();

        Asiento::where('id_urban', $this->urban->id_urban)->delete();

        $this->js("Flux.modal('modal-eliminar-urban').close()");
        $this->dispatch('urban-eliminada');
    }

    #[Computed]
    public function socios()
    {
        return Socio::orderBy('nombre')->get();
    }
};
?>

<div>
    <flux:modal name="modal-editar-urban" class="w-[60%] p-10">
        @if($urban)
            <flux:heading size="lg" class="mb-6">Editar Urban: {{ $urban->codigo_urban }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <flux:input wire:model="codigo_urban" icon:trailing="a-large-small" type="text"
                        label="Código de la Urban" description:trailing="Ingrese minimo 3 caracteres" />
                    @error('codigo_urban') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <flux:input wire:model="numero_asientos" icon:trailing="a-large-small" type="number"
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
                    <flux:input wire:model="placa" icon:trailing="a-large-small" type="text" label="Placa"
                        description:trailing="Ingrese la placa" />
                    @error('placa') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
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