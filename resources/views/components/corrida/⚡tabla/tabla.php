<?php

use App\Models\Corrida;
use App\Models\Urban;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

new class extends Component
{
    use WithPagination;

    public $sortBy = 'id_corrida';

    public $sortDirection = 'asc';

    public $search = '';

    public $perPage = 7;
    
    public $filtroEstado = '';

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[On('searchUpdated')]
    public function updateSearch($value)
    {
        $this->search = $value;
        $this->resetPage();
    }

    #[On('filterUpdated')]
    public function aplicarFiltro($filters)
    {
        $this->filtroEstado = $filters['estado'] ?? '';
        $this->resetPage();
    }


    #[On('corrida-creada')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[Computed]
    public function corridas()
    {
        return Corrida::query()
            ->when($this->search !== '', function ($query) {
                $query->whereHas('ruta', function ($q) {
                    $q->whereRaw('LOWER(nombre) like ?', ['%'.strtolower($this->search).'%']);
                })->orWhereHas('user', function ($q) {
                    $q->whereRaw('LOWER(name) like ?', ['%'.strtolower($this->search).'%']);
                });
            })
            ->when($this->filtroEstado === 'Programada', function ($query) {
                $query->where('estado', $this->filtroEstado);
            })
            ->when($this->filtroEstado === 'En viaje', function ($query) {
                $query->where('estado', $this->filtroEstado);
            })
            ->when($this->filtroEstado === 'Finalizada', function ($query) {
                $query->where('estado', $this->filtroEstado);
            })
            ->when($this->filtroEstado === 'Cancelada', function ($query) {
                $query->where('estado', $this->filtroEstado);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function urbans()
    {
        return Urban::orderBy('id_urban')->get();
    }

    
};
