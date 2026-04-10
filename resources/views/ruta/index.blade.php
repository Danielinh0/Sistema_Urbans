<x-layouts::app :title="__('ruta')">
    
    <section class="flex ">

        {{-- <div class="p-2">
            <livewire:rutas.form />
        </div> --}}
        

        <div class= "" >
            <flux:modal.trigger name="edit-profile">
                <flux:button icon="square-plus">Crea una nueva ruta </flux:button>
            </flux:modal.trigger>
            <livewire:rutas.tabla />
                

                <flux:modal name="edit-profile" class="w-[50%] p-10">
                    <div>
                    <flux:heading class="!text-xl !font-bold" size="lg">Crea una nueva ruta</flux:heading>        
                </div>

                    <livewire:rutas.form />
                </flux:modal>
        </div>

    </section>
       

</x-layouts::app>
