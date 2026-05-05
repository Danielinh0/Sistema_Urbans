<div class="space-y-6 pb-10">

    {{-- ── 1. Buscador ─────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm p-6">
        <h3 class="flex items-center gap-2 font-bold text-gray-800 dark:text-white text-base mb-5">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40">
                <flux:icon.bus class="size-4 text-blue-600 dark:text-blue-400" />
            </span>
            Buscar Corrida Disponible
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <flux:label class="mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    Fecha de Viaje
                </flux:label>
                <flux:input x-bind:min="new Date().toISOString().split('T')[0]" type="date" wire:model.live="filtroFecha" />
            </div>
            <div>
                <flux:label class="mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    Ruta / Destino
                </flux:label>
                <flux:input wire:model.live.debounce.300ms="filtroRuta"
                    placeholder="Ej: Santa Catarina Juquila…">
                    <x-slot name="iconLeading">
                        <flux:icon.map class="size-4" />
                    </x-slot>
                </flux:input>
            </div>
        </div>
    </div>

    {{-- ── 2. Corridas disponibles ─────────────────────────────── --}}
    <livewire:corrida.tabla-detalles-general
        modo="seleccion"
        :filtro-fecha="$filtroFecha"
        wire:key="tabla-corridas-{{ $filtroFecha }}" />

    {{-- ── 3. Selector asiento + Formulario (solo si hay corrida) ── --}}
    @if($corridaId)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" wire:key="form-section">

        {{-- 3a. Mapa de asientos ────────────────────────────────── --}}
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <flux:icon.layout-grid class="size-4 text-blue-600" />
                    Seleccionar Asiento
                </h3>
                @if($corridaData)
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-neutral-800 px-2.5 py-1 rounded-full">
                    {{ $corridaData['codigo_urban'] }}
                </span>
                @endif
            </div>

            {{-- Leyenda --}}
            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600 dark:text-gray-400 mb-5 pb-4 border-b border-gray-100 dark:border-neutral-800">
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-emerald-100 dark:bg-emerald-900/40 border-2 border-emerald-400 inline-block"></span>
                    Libre
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-red-100 dark:bg-red-900/40 border-2 border-red-400 inline-block"></span>
                    Ocupado
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-amber-100 dark:bg-amber-900/40 border-2 border-amber-400 inline-block"></span>
                    Apartado
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded bg-blue-600 border-2 border-blue-700 inline-block"></span>
                    Seleccionado
                </span>
            </div>

            {{-- Van visual --}}
            <div class="flex justify-center">
                <div class="relative select-none" style="width:270px">

                    {{-- Cuerpo de la van --}}
                    <div class="relative rounded-[2.5rem] border-[3px] border-gray-300 dark:border-neutral-600
                                bg-gradient-to-b from-slate-100 to-gray-50 dark:from-neutral-800 dark:to-neutral-900
                                px-5 pt-7 pb-10 shadow-lg">

                        {{-- Parabrisas --}}
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-28 h-7 rounded-b-2xl
                                    bg-sky-100/80 dark:bg-sky-900/30 border-b-2 border-sky-200 dark:border-sky-800"></div>

                        {{-- Espejos laterales --}}
                        <div class="absolute -left-4 top-10 w-4 h-7 bg-gray-300 dark:bg-neutral-600
                                    rounded-l-lg border border-gray-400 dark:border-neutral-500 shadow-sm"></div>
                        <div class="absolute -right-4 top-10 w-4 h-7 bg-gray-300 dark:bg-neutral-600
                                    rounded-r-lg border border-gray-400 dark:border-neutral-500 shadow-sm"></div>

                        {{-- Puerta deslizante (visual) --}}
                        <div class="absolute -right-1 top-1/4 w-1.5 h-16
                                    bg-gray-400 dark:bg-neutral-500 rounded-r-lg"></div>

                        {{-- Área del conductor y Copiloto (Fila 0) --}}
                        <div class="flex items-center justify-between mb-3 mt-1">
                            {{-- Lado Conductor --}}
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col items-center gap-1">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700
                        flex items-center justify-center shadow-md">
                                        <flux:icon.bus class="size-6 text-white" />
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500">Conductor</span>
                                </div>
                                @if($corridaData && $corridaData['chofer'])
                                <div>
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 leading-tight">
                                        {{ $corridaData['chofer'] }}
                                    </p>
                                </div>
                                @endif
                            </div>

                            {{-- Lado Copiloto (Aquí va el Asiento 3) --}}
                            <div class="flex flex-col items-center gap-1">
                                @if(isset($asientosOrganizados[0]['right'][0]))
                                @php
                                $seat = $asientosOrganizados[0]['right'][0];
                                $sc = match(true) {
                                $asientoId === $seat['id'] => 'bg-blue-600 border-blue-700 text-white shadow-lg ring-2 ring-blue-300 dark:ring-blue-700 scale-110 z-10',
                                $seat['estado'] === 'ocupado' => 'bg-red-100 dark:bg-red-900/40 border-red-400 dark:border-red-700 text-red-600 dark:text-red-400 cursor-not-allowed',
                                $seat['estado'] === 'apartado'=> 'bg-amber-100 dark:bg-amber-900/40 border-amber-400 dark:border-amber-700 text-amber-700 dark:text-amber-400 cursor-not-allowed',
                                default => 'bg-emerald-100 dark:bg-emerald-900/40 border-emerald-400 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-800/60 hover:scale-105 cursor-pointer',
                                };
                                @endphp
                                <button wire:click="seleccionarAsiento({{ $seat['id'] }})"
                                    class="relative w-11 h-11 rounded-xl text-[11px] font-bold border-2 transition-all duration-150 flex items-center justify-center {{ $sc }}"
                                    @if(in_array($seat['estado'], ['ocupado','apartado'])) disabled @endif>
                                    {{ $seat['nombre'] }}
                                </button>
                                @endif
                            </div>
                        </div>

                        {{-- Divisor pasajeros / conductor --}}
                        <div class="border-t-2 border-dashed border-gray-300 dark:border-neutral-600 mb-4 -mx-2"></div>

                        {{-- Filas de asientos --}}
                        <div class="space-y-3 relative z-10">
                            @foreach($asientosOrganizados as $fila => $lados)
                            @continue($fila == 0)

                            @php
                            $totalEnFila = count($lados['left']) + count($lados['right']);
                            $esFilaTrasera = $totalEnFila >= 4;
                            @endphp

                            <div class="flex items-center justify-center {{ $esFilaTrasera ? 'gap-1.5' : 'gap-4' }}">

                                {{-- Lado izquierdo (Doble asiento en la Crafter) --}}
                                <div class="flex gap-1.5 justify-end {{ $esFilaTrasera ? '' : 'min-w-[86px]' }}">
                                    @foreach($lados['left'] as $seat)
                                    @php
                                    $sc = match(true) {
                                    $asientoId === $seat['id'] => 'bg-blue-600 border-blue-700 text-white shadow-lg ring-2 ring-blue-300 dark:ring-blue-700 scale-110 z-10',
                                    $seat['estado'] === 'ocupado' => 'bg-red-100 dark:bg-red-900/40 border-red-400 dark:border-red-700 text-red-600 dark:text-red-400 cursor-not-allowed',
                                    $seat['estado'] === 'apartado'=> 'bg-amber-100 dark:bg-amber-900/40 border-amber-400 dark:border-amber-700 text-amber-700 dark:text-amber-400 cursor-not-allowed',
                                    default => 'bg-emerald-100 dark:bg-emerald-900/40 border-emerald-400 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-800/60 hover:scale-105 cursor-pointer',
                                    };
                                    @endphp
                                    <button wire:click="seleccionarAsiento({{ $seat['id'] }})"
                                        class="relative w-10 h-10 rounded-xl text-[11px] font-bold border-2 transition-all duration-150 flex items-center justify-center {{ $sc }}"
                                        @if(in_array($seat['estado'], ['ocupado','apartado'])) disabled @endif
                                        title="{{ $seat['nombre'] }} — {{ ucfirst($seat['estado']) }}">
                                        {{ $seat['nombre'] }}
                                    </button>
                                    @endforeach
                                </div>

                                {{-- Pasillo (Se oculta si es la fila trasera de 4 asientos) --}}
                                @if(!$esFilaTrasera)
                                <div class="w-4 flex justify-center">
                                    <div class="w-px h-8 border-l-2 border-dashed border-gray-300 dark:border-neutral-600"></div>
                                </div>
                                @endif

                                {{-- Lado derecho (Un asiento individual en la Crafter) --}}
                                <div class="flex gap-1.5 justify-start {{ $esFilaTrasera ? '' : 'min-w-[40px]' }}">
                                    @foreach($lados['right'] as $seat)
                                    @php
                                    $sc = match(true) {
                                    $asientoId === $seat['id'] => 'bg-blue-600 border-blue-700 text-white shadow-lg ring-2 ring-blue-300 dark:ring-blue-700 scale-110 z-10',
                                    $seat['estado'] === 'ocupado' => 'bg-red-100 dark:bg-red-900/40 border-red-400 dark:border-red-700 text-red-600 dark:text-red-400 cursor-not-allowed',
                                    $seat['estado'] === 'apartado'=> 'bg-amber-100 dark:bg-amber-900/40 border-amber-400 dark:border-amber-700 text-amber-700 dark:text-amber-400 cursor-not-allowed',
                                    default => 'bg-emerald-100 dark:bg-emerald-900/40 border-emerald-400 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-800/60 hover:scale-105 cursor-pointer',
                                    };
                                    @endphp
                                    <button wire:click="seleccionarAsiento({{ $seat['id'] }})"
                                        class="relative w-10 h-10 rounded-xl text-[11px] font-bold border-2 transition-all duration-150 flex items-center justify-center {{ $sc }}"
                                        @if(in_array($seat['estado'], ['ocupado','apartado'])) disabled @endif
                                        title="{{ $seat['nombre'] }} — {{ ucfirst($seat['estado']) }}">
                                        {{ $seat['nombre'] }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Parachoques trasero --}}
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-28 h-5
                                    bg-gray-200/80 dark:bg-neutral-700/60 border-t border-gray-300 dark:border-neutral-600
                                    rounded-b-[2.5rem]"></div>
                    </div>
                </div>
            </div>

            {{-- Asiento seleccionado --}}
            @if($asientoId)
            <div class="mt-4 flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl px-4 py-2.5 text-sm text-blue-700 dark:text-blue-300 font-semibold">
                <flux:icon.square-plus class="size-4 shrink-0" />
                Asiento seleccionado: {{ $asientoNombre }}
            </div>
            @else
            <p class="mt-4 text-center text-xs text-gray-400 dark:text-gray-500 italic">
                Toca un asiento libre para seleccionarlo
            </p>
            @endif
        </div>

        {{-- 3b. Datos del pasajero + Resumen ───────────────────── --}}
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm p-6 space-y-5">

            <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <flux:icon.user-round-pen class="size-4 text-blue-600" />
                Datos del Pasajero
            </h3>

            {{-- Buscador de cliente --}}
            <div class="relative" x-data="{ showResults: @entangle('mostrarResultados') }">
                <flux:label class="mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    Buscar cliente registrado
                </flux:label>
                <div class="relative flex items-center">
                    <flux:input
                        wire:model.live.debounce.300ms="busquedaCliente"
                        placeholder="Nombre o apellido…"
                        autocomplete="off" />
                    @if($clienteId)
                    <button wire:click="limpiarCliente"
                        class="absolute right-2.5 text-gray-400 hover:text-red-500 transition-colors"
                        title="Limpiar cliente">
                        <flux:icon.circle-minus class="size-4" />
                    </button>
                    @endif
                </div>

                {{-- Dropdown resultados --}}
                @if($mostrarResultados && count($clientesResultados) > 0)
                <div class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-xl overflow-hidden"
                    @click.outside="$wire.cerrarResultados()">
                    @foreach($clientesResultados as $c)
                    <button wire:click="seleccionarCliente({{ $c['id'] }})"
                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-left
                                   hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors
                                   border-b border-gray-100 dark:border-neutral-700 last:border-0">
                        <flux:icon.user-round class="size-4 text-gray-400 shrink-0" />
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $c['nombre'] }}</span>
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Nombre completo --}}
            <div>
                <flux:label class="mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    Nombre Completo <span class="text-red-500">*</span>
                </flux:label>
                <flux:input wire:model="nombreCompleto" placeholder="Ej: María López Hernández" />
                @error('nombreCompleto')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Peso equipaje + Tipo de pago --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:label class="mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        Peso Equipaje (kg)
                    </flux:label>
                    <flux:input type="number" step="0.1" min="0" wire:model="pesoEquipaje" placeholder="Ej: 15.5" />
                </div>
                <div>
                    <flux:label class="mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        Tipo de Pago
                    </flux:label>
                    <flux:select wire:model.live="tipoPago">
                        <option value="Efectivo">Efectivo</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Transferencia">Transferencia</option>
                    </flux:select>
                </div>
            </div>

            {{-- Categoría / Descuento --}}
            <div>
                <flux:label class="mb-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    Categoría / Descuento
                </flux:label>
                <flux:select wire:model.live="categoriaDescuento">
                    <option value="">Normal (sin descuento)</option>
                    <option value="estudiante">Estudiante (10%)</option>
                    <option value="adulto_mayor">Adulto Mayor (20%)</option>
                    <option value="nino">Niño (15%)</option>
                </flux:select>
            </div>

            {{-- ── Resumen del boleto ──────────────────────────── --}}
            <div class="rounded-xl border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800/60 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                    <h4 class="font-bold text-gray-800 dark:text-white text-sm">Resumen del Boleto</h4>
                </div>
                <div class="px-4 py-3 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span>Folio</span>
                        <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $folio }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span>Corrida</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">
                            @if($corridaData)
                            {{ $corridaData['hora_salida'] }} — {{ $corridaData['codigo_urban'] }}
                            @else
                            —
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span>Asiento</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $asientoNombre ?: '—' }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span>Segmento</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300 text-right max-w-40 truncate">
                            {{ ($abordarEn && $bajarEn) ? "$abordarEn → $bajarEn" : '—' }}
                        </span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-neutral-700 pt-2 mt-2">
                        <div class="flex justify-between text-gray-500 dark:text-gray-400">
                            <span>Tarifa base</span>
                            <span class="font-medium">${{ $corridaData ? $corridaData['tarifa'] : '0.00' }}</span>
                        </div>
                        @if($descuento > 0)
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                            <span>Descuento aplicado</span>
                            <span class="font-medium">— ${{ number_format($descuento, 2) }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="border-t border-gray-200 dark:border-neutral-700 pt-2 flex justify-between items-center">
                        <span class="font-bold text-gray-800 dark:text-white text-base">Total a Pagar</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400 text-lg">${{ number_format($totalAPagar, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500 dark:text-gray-400 text-xs pt-1">
                        <span>Tipo de pago</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $tipoPago }}</span>
                    </div>
                </div>
            </div>

            {{-- ── Botones de acción ───────────────────────────── --}}
            <div class="flex flex-wrap gap-2 pt-1">
                <flux:button wire:click="confirmarVenta" variant="primary" class="flex-1">
                    Confirmar Venta
                </flux:button>
                <flux:button wire:click="apartar" variant="outline" class="flex-1">
                    Apartar
                </flux:button>
                <flux:button wire:click="cancelar" variant="ghost">
                    Cancelar
                </flux:button>
            </div>

        </div>{{-- /form card --}}
    </div>{{-- /grid --}}
    @endif

    {{-- ── Toast notification ──────────────────────────────────── --}}
    @if($flashMsg)
    <div
        x-data="{ visible: true }"
        x-init="setTimeout(() => { visible = false; $wire.set('flashMsg', '') }, 4000)"
        x-show="visible"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        wire:key="toast-{{ $flashMsg }}"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-4 rounded-2xl shadow-2xl text-sm font-medium max-w-sm
        {{ $flashType === 'success'
            ? 'bg-emerald-50 dark:bg-emerald-900/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700'
            : 'bg-red-50 dark:bg-red-900/80 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700' }}">
        @if($flashType === 'success')
        <flux:icon.circle-plus class="size-5 shrink-0 text-emerald-500" />
        @else
        <flux:icon.circle-minus class="size-5 shrink-0 text-red-500" />
        @endif

        <span class="flex-1">{{ $flashMsg }}</span>

        <button
            x-on:click="visible = false; $wire.set('flashMsg', '')"
            class="ml-1 opacity-50 hover:opacity-100 transition-opacity">
            <flux:icon.x class="size-4" />
        </button>
    </div>
    @endif



</div>