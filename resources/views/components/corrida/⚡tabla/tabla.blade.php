<div>
    
        <flux:table :paginate="$this->corridas">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'id_corrida'" :direction="$sortDirection"
                    wire:click="sort('id_corrida')">ID</flux:table.column>

                <flux:table.column sortable :sorted="$sortBy === 'id_ruta'" :direction="$sortDirection"
                    wire:click="sort('id_ruta')">Ruta</flux:table.column>

                <flux:table.column>Urban</flux:table.column>    

                <flux:table.column sortable :sorted="$sortBy === 'id_usuario'" :direction="$sortDirection"
                    wire:click="sort('id_usuario')">Conductor</flux:table.column>


                <flux:table.column sortable :sorted="$sortBy === 'fecha'" :direction="$sortDirection"
                    wire:click="sort('fecha')">Fecha</flux:table.column>

                <flux:table.column sortable :sorted="$sortBy === 'hora_salida'" :direction="$sortDirection"
                wire:click="sort('hora_salida')">Hora de Salida</flux:table.column>

                <flux:table.column sortable :sorted="$sortBy === 'hora_llegada'" :direction="$sortDirection"
                    wire:click="sort('hora_llegada')">Hora de Llegada</flux:table.column>

                <flux:table.column align="center">Acciones</flux:table.column>

            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->corridas as $corrida)
                    <flux:table.row :key="$corrida->id_corrida">
                        <flux:table.cell>
                            {{ $corrida->id_corrida }}
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            {{ $corrida->ruta->nombre }}
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-normal">
                            @forelse ($corrida->urbans as $urban)
                                <div>{{ $urban->codigo_urban }}</div>
                            @empty
                                <span class="text-zinc-500">Sin urban</span>
                            @endforelse
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $corrida->user->name }} 
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $corrida->fecha }}
                        </flux:table.cell>
                        <flux:table.cell variant="strong">
                            {{ $corrida->hora_salida }}
                        </flux:table.cell>
                        <flux:table.cell variant="strong">
                            {{ $corrida->hora_llegada }}
                        </flux:table.cell>
                        
                        <flux:table.cell class="flex gap-1">

                            <x-boton-estilo bg="bg-azul_menu" c_text="text-white" icon="map-pin-pen" text="Editar" 
                            evento=""/>

                            {{-- <x-boton-estilo bg="bg-rojo_boton" c_text="text-rojo_texto" icon="map-pin-x" text="Eliminar" 
                            evento="$dispatch('eliminacion-ruta', { id: {{ $ruta->id_ruta }} })" /> --}}

                        </flux:table.cell>

                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-4 ">
                            No se encontraron corridas.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    
    {{-- <livewire:rutas.modal /> --}}
</div>