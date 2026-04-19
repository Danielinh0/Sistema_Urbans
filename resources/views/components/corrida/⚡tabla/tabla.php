<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\Corrida;
use App\Models\Urban;

new class extends Component
{
    use WithPagination;
    public $sortBy = 'id_corrida';
    public $sortDirection = 'desc';
    public $search = '';
    public $perPage = 7;

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
                    $q->whereRaw('LOWER(nombre) like ?', ['%' . strtolower($this->search) . '%']);
                })->orWhereHas('user', function ($q) {
                    $q->whereRaw('LOWER(name) like ?', ['%' . strtolower($this->search) . '%']);
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function urbans(){
        return Urban::orderBy('id_urban')->get();
    }

};
