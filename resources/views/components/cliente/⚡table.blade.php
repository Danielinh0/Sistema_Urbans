<?php

use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Cliente;

new class extends Component
{
    use WithPagination;
    
    public $sortBy = 'id_cliente';
    public $sortDirection = 'desc';
    public $search = '';

    public function sort($column) {
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

    #[On('cliente-creado')]
    #[On('cliente-eliminado')]
    #[On('cliente-actualizado')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[Computed]
    public function clientes(){
        return Cliente::query()
            ->when($this->search !== '', function ($query) {
                $query->whereRaw('LOWER(nombre) like ?', ['%'.strtolower($this->search).'%']);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }
};
?>

<div>
    <div>
        <flux:card>
            <flux:table>
                <flux:table.columns>
                    

                </flux:table.columns>
            </flux:table>
        </flux:card>
    </div>
</div>