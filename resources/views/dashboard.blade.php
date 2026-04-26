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
            @if(auth()->user()->hasRole('gerente'))
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
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">

            {{-- Header de la sección --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-white text-base">Próximas Salidas</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Hoy · {{ $fecha_hoy }}</p>
                    </div>
                </div>
                <a href="{{ route('corrida.index') }}"
                    class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                    Ver todas →
                </a>
            </div>

            {{-- Tabla --}}
            @if($corridas->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-neutral-800 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Sin corridas programadas para hoy</p>
                <p class="text-gray-400 dark:text-gray-600 text-sm mt-1">Las salidas del día aparecerán aquí automáticamente.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-neutral-800/60">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unidad</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Destino</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Asientos</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                        @foreach ($corridas as $corrida)
                        <tr class="hover:bg-blue-50/40 dark:hover:bg-blue-900/10 transition-colors duration-150">

                            {{-- Hora --}}
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-800 dark:text-white text-base tracking-tight">
                                    {{ $corrida['hora_salida'] }}
                                </span>
                            </td>

                            {{-- Unidad --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 font-semibold text-gray-700 dark:text-gray-300">
                                    <span class="w-2 h-2 rounded-full bg-blue-400 dark:bg-blue-500"></span>
                                    {{ $corrida['codigo_urban'] }}
                                </span>
                            </td>

                            {{-- Destino --}}
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-800 dark:text-white">
                                    {{ $corrida['ruta'] }}
                                </span>
                            </td>

                            {{-- Badge de asientos --}}
                            <td class="px-6 py-4 text-center">
                                @if ($corrida['lleno'])
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 ring-1 ring-red-200 dark:ring-red-800">
                                    Lleno
                                </span>
                                @elseif ($corrida['total_asientos'] === 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 dark:bg-neutral-800 dark:text-gray-500 ring-1 ring-gray-200 dark:ring-neutral-700">
                                    Sin asientos
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-800">
                                    {{ $corrida['asientos_libres'] }} Libres
                                </span>
                                @endif
                            </td>

                            {{-- Botón Detalles --}}
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('corrida.show', $corrida['id']) }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-semibold shadow-sm hover:shadow transition-all duration-150">
                                    Detalles
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>


</x-layouts::app>