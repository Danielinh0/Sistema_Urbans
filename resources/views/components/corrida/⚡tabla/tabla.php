<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\Corrida;

new class extends Component
{
    use WithPagination;
    public $sortBy = 'id_corrida';
    public $sortDirection = 'desc';
    public $search = '';

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

    #[On('corrida-creada')]
    #[On('corrida-actualizada')]
    #[On('corrida-eliminada')]
    public function refreshAfterChange()
    {
        $this->resetPage();
    }

    #[Computed]
    public function corridas()
    {
        return Corrida::query()
            ->with(['ruta', 'manejadas.urbans', 'manejadas.usuarios'])
            ->when($this->search !== '', function ($query) {
                $search = strtolower($this->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('ruta', function ($rutaQuery) use ($search) {
                        $rutaQuery->whereRaw('LOWER(nombre) like ?', ['%' . $search . '%']);
                    })->orWhereHas('manejadas.urbans', function ($urbanQuery) use ($search) {
                        $urbanQuery->whereRaw('LOWER(codigo_urban) like ?', ['%' . $search . '%']);
                    })->orWhereHas('manejadas.usuarios', function ($userQuery) use ($search) {
                        $userQuery->whereRaw('LOWER(name) like ?', ['%' . $search . '%']);
                    });
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

};
