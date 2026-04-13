
<div>
    <flux:modal name="modal-editar-corrida" class="w-[60%] p-10">
        @if($corrida)
            <div class="space-y-6">
                <flux:heading size="lg">Editar corrida #{{ $corrida->id_corrida }}</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label badge="Obligatorio">Ruta</flux:label>
                        <flux:select wire:model="id_ruta" placeholder="Selecciona una ruta">
                            @foreach ($this->rutas as $ruta)
                                <flux:select.option value="{{ $ruta->id_ruta }}">
                                    {{ $ruta->nombre }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="id_ruta" />
                    </flux:field>

                    <flux:field>
                        <flux:label badge="Obligatorio">Conductor</flux:label>
                        <flux:select wire:model="id_usuario" placeholder="Conductor">
                            @foreach ($this->usuarios as $conductor)
                                <flux:select.option value="{{ $conductor->id_usuario }}">
                                    {{ $conductor->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="id_usuario" />
                    </flux:field>

                    <flux:input wire:model="fecha" type="date" label="Fecha" badge="Obligatorio" />
                    <x-input-time wire="hora_salida" texto="Hora de salida" />
                    <x-input-time wire="hora_llegada" texto="Hora de llegada" />
                </div>

                <flux:field>
                    <flux:label class="mb-2!" badge="Obligatorio">Urbans asignadas</flux:label>

                    @if ($this->urbansSeleccionadas->isNotEmpty())
                        <div class="rounded-lg border border-zinc-200 dark:border-white/10 p-2 flex flex-wrap gap-2 mb-3">
                            @foreach ($this->urbansSeleccionadas as $urban)
                                <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 dark:bg-white/10 px-2 py-1 text-xs">
                                    <span>{{ $urban->codigo_urban }}</span>
                                    <button
                                        type="button"
                                        wire:click="quitarUrban({{ $urban->id_urban }})"
                                        class="text-zinc-500 hover:text-red-500"
                                        aria-label="Quitar urban"
                                    >
                                        x
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <flux:select wire:model="id_urban_actual" placeholder="Selecciona una urban">
                        @foreach ($this->urbans as $urban)
                            @if (!in_array($urban->id_urban, $id_urbans))
                                <flux:select.option value="{{ $urban->id_urban }}">
                                    {{ $urban->codigo_urban }}
                                </flux:select.option>
                            @endif
                        @endforeach
                    </flux:select>

                    <div class="mt-2">
                        <flux:button type="button" wire:click="agregarUrban" variant="subtle">Agregar urban</flux:button>
                    </div>

                    <flux:error name="id_urbans" />
                </flux:field>

                <flux:button wire:click="update" variant="primary" class="w-full">Guardar cambios</flux:button>
            </div>
        @endif
    </flux:modal>

    <flux:modal name="modal-eliminar-corrida" class="min-w-88">
        @if($corrida)
            <div class="space-y-6">
                <flux:heading size="lg">Eliminar corrida</flux:heading>
                <flux:text>
                    ¿Estas seguro de eliminar la corrida de la ruta <b>{{ $corrida->ruta?->nombre ?? 'Sin ruta' }}</b> del dia <b>{{ $corrida->fecha }}</b>?
                </flux:text>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:button wire:click="delete" variant="danger">Eliminar</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>