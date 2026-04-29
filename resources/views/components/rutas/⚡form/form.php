<?php

use Livewire\Attributes\Validate;
use App\Models\Ruta;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    #[Validate('required', message: 'El nombre de la ruta es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    #[Validate('unique:ruta,nombre', message: 'Ya existe una ruta con este nombre.')]
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


    public function touchField(string $field): void
    {
        $this->validateOnly($field);
    }

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    #[Computed]
    public function formularioListo(): bool
    {
        return filled($this->nombre)
            && filled($this->distancia)
            && filled($this->tiempo_estimado)
            && filled($this->tarifa_clientes)
            && filled($this->tarifa_paquete)
            && $this->getErrorBag()->isEmpty();
    }

    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset(['nombre', 'distancia', 'tiempo_estimado', 'tarifa_clientes', 'tarifa_paquete']);
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        Ruta::create([
            'nombre' => $this->nombre,
            'distancia' => $this->distancia,
            'tiempo_estimado' => $this->tiempo_estimado,
            'tarifa_clientes' => $this->tarifa_clientes,
            'tarifa_paquete' => $this->tarifa_paquete,
        ]);

        $this->reset(['nombre', 'distancia', 'tiempo_estimado', 'tarifa_clientes', 'tarifa_paquete']);
        $this->dispatch('ruta-creada');
        session()->flash('status', 'Ruta creada correctamente.');
    }
};
