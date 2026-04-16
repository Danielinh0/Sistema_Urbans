<?php

use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\User;

new class extends Component
{
    use WithPagination;

    public $sortBy = 'id_usuario';
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

    #[On('usuario-creado')]
    #[On('usuario-eliminado')]
    #[On('usuario-actualizado')]
    public function refreshAfterCreate()
    {
        $this->resetPage();
    }

    #[Computed]
    public function usuarios(){
        return User::query()
            ->when($this->search !== '', function ($query) {
                $query->whereRaw('LOWER(name) like ?', ['%'.strtolower($this->search).'%']);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }
};
?>

<div>
    <div>
        <flux:card>
            <flux:table :paginate="$this->usuarios">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'id_usuario'" :direction="$sortDirection" wire:click="sort('id_usuario')">ID</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Nombre</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection" wire:click="sort('email')">Email</flux:table.column>
                    <flux:table.column sortable>Sucursal</flux:table.column>
                    <flux:table.column sortable>Tipo de usuario</flux:table.column>
                    <flux:table.column>Acciones</flux:table.column>
                </flux:table.columns>
            </flux:table>
        </flux:card>
    </div>
</div>