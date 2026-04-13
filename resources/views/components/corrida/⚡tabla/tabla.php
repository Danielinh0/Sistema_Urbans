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

    // #[On('ruta-eliminada')]
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
                $query->whereRaw('LOWER(nombre) like ?', ['%' . strtolower($this->search) . '%']);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    #[Computed]
    public function urbans(){
        return Urban::orderBy('id_urban')->get();
    }

};
