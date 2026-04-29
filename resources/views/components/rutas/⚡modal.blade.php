<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Ruta;
use Livewire\Attributes\On;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

new class extends Component
{
    use AuthorizesRequests;

    public ?Ruta $ruta = null;
    #[Validate('required', message: 'El nombre de la ruta es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    public $nombre;

    #[Validate('required', message: 'La distancia es requerida.')]
    #[Validate('numeric', message: 'La distancia debe ser un valor numérico.')]
    #[Validate('min:0.1', message: 'La distancia debe ser mayor a 0.')]
    public $distancia;

    #[Validate('required', message: 'El tiempo estimado es requerido.')]
    #[Validate('date_format:H:i', message: 'El tiempo estimado debe estar en formato HH:MM.')]
    public $tiempo_estimado;

    #[Validate('required', message: 'La tarifa para personas es requerida.')]
    #[Validate('numeric', message: 'La tarifa debe ser un valor numérico.')]
    #[Validate('min:1', message: 'La tarifa debe ser mayor a 0.')]
    public $tarifa_clientes;

    #[Validate('required', message: 'La tarifa para paquetes es requerida.')]
    #[Validate('numeric', message: 'La tarifa debe ser un valor numérico.')]
    #[Validate('min:1', message: 'La tarifa debe ser mayor a 0.')]
    public $tarifa_paquete;

    #[On('edicion-ruta')]
    public function prepararEdicion($id)
    {
        $this->ruta = Ruta::findOrFail($id);
        $this->authorize('update', $this->ruta);
        $this->nombre = $this->ruta->nombre;
        $this->distancia = $this->ruta->distancia;
        $this->tiempo_estimado = $this->ruta->tiempo_estimado;
        $this->tarifa_clientes = $this->ruta->tarifa_clientes;
        $this->tarifa_paquete = $this->ruta->tarifa_paquete;

        $this->js("Flux.modal('modal-editar-ruta').show()");
    }

    #[On('eliminacion-ruta')]
    public function prepararEliminacion($id)
    {
        $this->ruta = Ruta::findOrFail($id);
        $this->authorize('delete', $this->ruta);
        $this->js("Flux.modal('modal-eliminar-ruta').show()");
    }

    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset(['nombre', 'distancia', 'tiempo_estimado', 'tarifa_clientes', 'tarifa_paquete']);
        $this->resetErrorBag();
    }

    public function touchField(string $field): void
    {
        $this->validateOnly($field);
    }

    public function update()
    {
        $this->authorize('update', $this->ruta);

        $this->validate([
            'nombre' => 'required|min:3|unique:ruta,nombre,' . $this->ruta->id_ruta . ',id_ruta',
            'distancia' => 'required|numeric|min:0.1',
            'tiempo_estimado' => 'required|date_format:H:i',
            'tarifa_clientes' => 'required|numeric|min:1',
            'tarifa_paquete' => 'required|numeric|min:1',
        ], [
            'nombre.required' => 'El nombre de la ruta es requerido.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.unique' => 'Ya existe una ruta con este nombre.',
            'distancia.required' => 'La distancia es requerida.',
            'distancia.numeric' => 'La distancia debe ser un valor numérico.',
            'distancia.min' => 'La distancia debe ser mayor a 0.',
            'tiempo_estimado.required' => 'El tiempo estimado es requerido.',
            'tiempo_estimado.date_format' => 'El tiempo estimado debe estar en formato HH:MM.',
            'tarifa_clientes.required' => 'La tarifa para personas es requerida.',
            'tarifa_clientes.numeric' => 'La tarifa debe ser un valor numérico.',
            'tarifa_clientes.min' => 'La tarifa debe ser mayor a 0.',
            'tarifa_paquete.required' => 'La tarifa para paquetes es requerida.',
            'tarifa_paquete.numeric' => 'La tarifa debe ser un valor numérico.',
            'tarifa_paquete.min' => 'La tarifa debe ser mayor a 0.',
        ]);

        $this->ruta->update([
            'nombre' => $this->nombre,
            'distancia' => $this->distancia,
            'tiempo_estimado' => $this->tiempo_estimado,
            'tarifa_clientes' => $this->tarifa_clientes,
            'tarifa_paquete' => $this->tarifa_paquete,
        ]);

        $this->js("Flux.modal('modal-editar-ruta').close()");
        $this->dispatch('ruta-creada');
    }

    public function delete()
    {
        $this->authorize('delete', $this->ruta);

        $this->ruta->delete();
        $this->js("Flux.modal('modal-eliminar-ruta').close()");
        $this->dispatch('ruta-eliminada');
    }
};
?>

<div>
    <flux:modal name="modal-editar-ruta" class="w-8/10 xl:w-[60%] xl:p-10" x-on:close="Livewire.dispatch('reset-form')">
        @if($ruta)
        <flux:heading class="mb-4" size="lg">Editar la ruta: {{ $ruta->nombre }}</flux:heading>
        <flux:card>
            <x-skeleton-form-ruta />
        </flux:card>

        <div class="mt-8">
            <flux:button wire:click="update" variant="primary" class="w-full">Guardar Cambios</flux:button>
        </div>
        @endif
    </flux:modal>

    <flux:modal name="modal-eliminar-ruta" class="w-[40%] md:min-w-22rem] ">
        @if($ruta)
        <div class="space-y-6">
            <flux:heading size="lg">Eliminar Ruta</flux:heading>
            <flux:text>
                ¿Estás seguro de que deseas eliminar la ruta <b>{{ $ruta->nombre }}</b>?
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