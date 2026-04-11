<?php
use Livewire\Attributes\Validate;
use Livewire\Component; 
use App\Models\Ruta;
  

new class extends Component
{   
      
    public $ruta;
    public $tipo = 'editar';

    public $bg = 'azul_menu';
    public $c_text = 'white';
    public $icon = 'map-pin-pen';
    public $text = 'Editar';
    public $esqueleto = '';

    #[Validate('required', message: 'El nombre de la ruta es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    public $nombre;

    #[Validate('required', message: 'La distancia es requerida.')]
    #[Validate('numeric', message: 'La distancia debe ser un valor numérico.')]
    #[Validate('min:0', message: 'La distancia no puede ser negativa.')]
    public $distancia;

    #[Validate('required', message: 'El tiempo estimado es requerido.')]
    #[Validate('date_format:H:i', message: 'El tiempo estimado debe estar en formato HH:MM.')]
    public $tiempo_estimado;

    #[Validate('required', message: 'La tarifa para personas es requerida.')]
    #[Validate('numeric', message: 'La tarifa debe ser un valor numérico.')]
    #[Validate('min:0', message: 'La tarifa no puede ser negativa.')]
    public $tarifa_clientes;

    #[Validate('required', message: 'La tarifa para paquetes es requerida.')]
    #[Validate('numeric', message: 'La tarifa debe ser un valor numérico.')]
    #[Validate('min:0', message: 'La tarifa no puede ser negativa.')]
    public $tarifa_paquete;       

    public $modalName;

    public function mount() {
        if ($this->ruta) {
            $this->nombre = $this->ruta->nombre;
            $this->distancia = $this->ruta->distancia;
            $this->tiempo_estimado = $this->ruta->tiempo_estimado;
            $this->tarifa_clientes = $this->ruta->tarifa_clientes;
            $this->tarifa_paquete = $this->ruta->tarifa_paquete;
            $this->modalName = 'modal-' . $this->ruta->id_ruta . '-' . \Illuminate\Support\Str::slug($this->text);
        }
    }

    public function update(){
        $id = Ruta::findOrFail($this->ruta->id_ruta);
        
        $this->validate(); 
        
        $id->update([
            'nombre' => $this->nombre,
            'distancia' => $this->distancia,
            'tiempo_estimado' => $this->tiempo_estimado,
            'tarifa_clientes' => $this->tarifa_clientes,
            'tarifa_paquete' => $this->tarifa_paquete
        ]);
        
        \Flux::modal($this->modalName)->close();
        $this->dispatch('ruta-creada');
    }

    public function delete(){
        $id = Ruta::findOrFail($this->ruta->id_ruta);
        $id->delete();
        
        \Flux::modal($this->modalName)->close();
        $this->dispatch('ruta-creada');
    }
};  
?>

<div>

    @if ($tipo === 'editar')
        <flux:modal.trigger name="{{ $modalName }}">
            <flux:button type="button" class="!bg-{{ $bg }} !text-{{ $c_text }} 
            transition delay-150 duration-300 ease-in-out hover:-translate-y-1 hover:scale-100 
            hover:bg-{{ $bg }}/110" icon="{{ $icon }}"> {{ $text }} </flux:button>
        </flux:modal.trigger>
        
        <flux:modal name="{{ $modalName }}" class="w-[50%] p-10">
           <div>
               <flux:heading class="!text-xl !font-bold" size="lg">Actualiza la ruta: {{ $ruta->nombre }} (ID: {{ $ruta->id_ruta }})</flux:heading>        
               
               <x-dynamic-component :component="$esqueleto" />
               
                   <div class="mt-5">
                       <flux:button  wire:click="update" variant="primary" class="w-full">Actualizar Ruta             </flux:button>
                   </div>            
           </div>
       </flux:modal>
        
    @else
       <flux:modal.trigger name="{{ $modalName }}">
            <flux:button type="button" class="!bg-{{ $bg }} !text-{{ $c_text }} 
            transition delay-150 duration-300 ease-in-out hover:-translate-y-1 hover:scale-100 
            hover:bg-{{ $bg }}/110" icon="{{ $icon }}"> {{ $text }} </flux:button>
        </flux:modal.trigger>

        <flux:modal name="{{ $modalName }}" class="min-w-[22rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Eliminar Ruta</flux:heading>
                    <flux:text class="mt-2">
                        Estás a punto de eliminar la ruta {{ $ruta->nombre }}.<br>
                        Esta acción no se puede deshacer.
                    </flux:text>
                </div>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:button wire:click="delete" variant="danger">Eliminar Ruta   </flux:button>
                </div>
            </div>
        </flux:modal>
        
    @endif

    


</div>