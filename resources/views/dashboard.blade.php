<x-layouts::app :title="__('Dashboard')">

    {{-- ══════════════════════════════════════════════
         AVISO DE TURNO NO INICIADO (Superior)
    ══════════════════════════════════════════════ --}}
    @if(auth()->user()->hasRole('cajero') && !$hayTurnoActivo)
    <div class="mb-6 relative overflow-hidden group">
        <div class="absolute inset-0 bg-amber-400/10 dark:bg-amber-400/5 animate-pulse rounded-2xl"></div>
        <div class="relative flex flex-col sm:flex-row items-center justify-between gap-4 p-4 border border-amber-200 dark:border-amber-800/50 rounded-2xl bg-white/50 dark:bg-zinc-900/50 backdrop-blur-md shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-400 flex items-center justify-center shadow-lg shadow-amber-400/20">
                    <svg class="w-6 h-6 text-amber-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-base font-bold text-amber-900 dark:text-amber-400 leading-tight">Turno pendiente de apertura</h4>
                    <p class="text-sm text-amber-800/70 dark:text-amber-500/70">Las funciones de venta y paquetería se encuentran deshabilitadas hasta que inicies un turno.</p>
                </div>
            </div>
            <flux:button href="{{ route('turno.create') }}" variant="primary" icon="key" class="w-full sm:w-auto shadow-lg shadow-blue-600/20">
                Abrir mi Turno ahora
            </flux:button>
        </div>
    </div>
    @endif

    {{-- Contenedor principal con opacidad si no hay turno --}}
    <div class="{{ (auth()->user()->hasRole('cajero') && !$hayTurnoActivo) ? 'opacity-50 pointer-events-none grayscale-[0.5] transition-all duration-500' : '' }}">


        {{-- ══════════════════════════════════════════════
         BOTONES DE ACCIÓN RÁPIDA
    ══════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">

            {{-- Botón 1: Venta rápida de boletos --}}
            @if(auth()->user()->hasRole('cajero') )
            <a href="{{ route('venta.create') }}"
                class="group relative overflow-hidden rounded-2xl aspect-[4/2.2] shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                <img src="{{ asset('images/btnr1.jpg') }}"
                    alt="Venta rápida de boletos"
                    class="absolute inset-0 w-full h-full object-cover scale-100 group-hover:scale-105 transition-transform duration-500">
                {{-- Overlay degradado --}}
                <div class="absolute inset-0 bg-gradient-to-br from-violet-800/70 via-purple-700/50 to-transparent"></div>
                {{-- Contenido --}}
                <div class="relative flex flex-col justify-between h-full p-5">
                    <h2 class="text-white font-extrabold text-xl leading-tight drop-shadow-lg max-w-[65%]">
                        Venta rápida de boletos
                    </h2>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 bg-amber-400 text-gray-900 text-xs font-bold px-3 py-1.5 rounded-full shadow">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $fecha_hoy }}
                        </span>
                        <span class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-900/80 group-hover:bg-blue-700 transition-colors shadow-lg">
                            <svg class="w-4 h-4 text-white translate-x-0 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
            @endif

            {{-- Botón 2: Encargo de Paquetería --}}
            @if(auth()->user()->hasRole('cajero'))
            <a href="#" {{-- route futura --}}
                class="group relative overflow-hidden rounded-2xl aspect-[4/2.2] shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                <img src="{{ asset('images/btnr2.webp') }}"
                    alt="Encargo de Paquetería"
                    class="absolute inset-0 w-full h-full object-cover scale-100 group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-600/75 via-amber-500/50 to-transparent"></div>
                <div class="relative flex flex-col justify-between h-full p-5">
                    <h2 class="text-white font-extrabold text-xl leading-tight drop-shadow-lg max-w-[65%]">
                        Encargo de Paquetería
                    </h2>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 bg-amber-400 text-gray-900 text-xs font-bold px-3 py-1.5 rounded-full shadow">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $fecha_hoy }}
                        </span>
                        <span class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-900/80 group-hover:bg-blue-700 transition-colors shadow-lg">
                            <svg class="w-4 h-4 text-white group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
            @endif

            {{-- Botón exclusivo de gerente: Abrir Taquilla --}}
            @if(auth()->user()->hasAnyRole(['gerente', 'admin']))
            <a href="#" {{-- route futura --}}
                class="group relative overflow-hidden rounded-2xl aspect-[4/2.2] shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                <img src="{{ asset('images/btnat.png') }}"
                    alt="Abrir Taquillas"
                    class="absolute inset-0 w-full h-full object-cover scale-100 group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-br from-green-600/75 via-green-500/50 to-transparent"></div>
                <div class="relative flex flex-col justify-between h-full p-5">
                    <h2 class="text-white font-extrabold text-xl leading-tight drop-shadow-lg max-w-[65%]">
                        Abrir Taquillas
                    </h2>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 bg-amber-400 text-gray-900 text-xs font-bold px-3 py-1.5 rounded-full shadow">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $fecha_hoy }}
                        </span>
                        <span class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-900/80 group-hover:bg-blue-700 transition-colors shadow-lg">
                            <svg class="w-4 h-4 text-white group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
            @endif

            {{-- Botón 3: Reporte de Ventas --}}
            <a href="#" {{-- route futura --}}
                class="group relative overflow-hidden rounded-2xl aspect-[4/2.2] shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                <img src="{{ asset('images/btnr3.webp') }}"
                    alt="Reporte de Ventas"
                    class="absolute inset-0 w-full h-full object-cover scale-100 group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-br from-sky-800/70 via-blue-600/50 to-transparent"></div>
                <div class="relative flex flex-col justify-between h-full p-5">
                    <h2 class="text-white font-extrabold text-xl leading-tight drop-shadow-lg max-w-[65%]">
                        Reporte de Ventas
                    </h2>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 bg-amber-400 text-gray-900 text-xs font-bold px-3 py-1.5 rounded-full shadow">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $fecha_hoy }}
                        </span>
                        <span class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-900/80 group-hover:bg-blue-700 transition-colors shadow-lg">
                            <svg class="w-4 h-4 text-white group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </a>

        </div>

        {{-- ══════════════════════════════════════════════
         PRÓXIMAS SALIDAS DEL DÍA
    ══════════════════════════════════════════════ --}}
        <livewire:corrida.tabla-detalles-general
            modo="vista"
            :filtro-fecha="today()->format('Y-m-d')"
            :permitir-cambiar-fecha="false"
            wire:key="tabla-corridas-hoy" />


</x-layouts::app>