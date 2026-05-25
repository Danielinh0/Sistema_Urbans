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
    #[On('corrida-actualizada')]
    #[On('corrida-eliminada')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[Computed]
    public function corridas()
    {
        return Corrida::query()
            ->where('estado', 'Programada')
            ->whereDate('datetime_salida', today())
            ->when($this->search !== '', function ($query) {
                $query->whereHas('ruta', function ($q) {
                    $q->whereRaw('LOWER(nombre) like ?', ['%'.strtolower($this->search).'%']);
                })->orWhereHas('user', function ($q) {
                    $q->whereRaw('LOWER(name) like ?', ['%'.strtolower($this->search).'%']);
                });
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
