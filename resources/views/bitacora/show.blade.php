<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora de Viaje - Corrida #{{ $bitacora['corrida']['id_corrida'] }}</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via CDN to guarantee instant styling rendering, plus customized overrides) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        @media print {
            body {
                background-color: #ffffff;
                color: #000000;
                font-size: 12px;
            }
            .no-print {
                display: none !important;
            }
            .print-card {
                border: 1px solid #cbd5e1 !important;
                box-shadow: none !important;
                background: #ffffff !important;
                margin-bottom: 1.5rem !important;
                page-break-inside: avoid;
            }
            .print-header {
                border-bottom: 2px solid #000000 !important;
                padding-bottom: 1rem !important;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Action Buttons (Hidden on Print) -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 no-print glass-card p-4 rounded-2xl shadow-sm">
            <a href="/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all duration-200 hover:scale-[1.02]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al Panel
            </a>
            
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 active:bg-indigo-800 shadow-md hover:shadow-indigo-200 shadow-indigo-100 transition-all duration-200 hover:scale-[1.02]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Imprimir Bitácora
            </button>
        </div>

        <!-- Header -->
        <div class="print-header flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 border-b border-slate-200">
            <div>
                <span class="px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-full uppercase tracking-wider">
                    Bitácora de Viaje Oficial
                </span>
                <h1 class="text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">Manifiesto de Corrida #{{ $bitacora['corrida']['id_corrida'] }}</h1>
                <p class="text-slate-500 text-sm mt-1">Generado el {{ now()->format('d/m/Y g:i A') }}</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="text-right md:text-left">
                    <span class="text-xs text-slate-400 block uppercase font-semibold">Unidad</span>
                    <span class="text-lg font-bold text-slate-800">{{ $bitacora['corrida']['unidad_urban'] }}</span>
                </div>
            </div>
        </div>

        <!-- Corrida Metadata Cards -->
        <div class="print-card grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="glass-card p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ruta & Trayecto</span>
                <div class="mt-2">
                    <span class="text-base font-bold text-slate-800 block">{{ $bitacora['corrida']['ruta'] }}</span>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-1">
                        <span>{{ $bitacora['corrida']['origen'] }}</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        <span>{{ $bitacora['corrida']['destino'] }}</span>
                    </div>
                </div>
            </div>

            <div class="glass-card p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Chofer Asignado</span>
                <div class="mt-2">
                    <span class="text-base font-bold text-slate-800 block">{{ $bitacora['corrida']['chofer'] }}</span>
                    <span class="text-xs text-indigo-600 font-semibold block mt-1">Operador Urbans</span>
                </div>
            </div>

            <div class="glass-card p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Horario de Salida</span>
                <div class="mt-2">
                    <span class="text-base font-bold text-slate-800 block">{{ $bitacora['corrida']['hora_salida'] }}</span>
                    <span class="text-xs text-slate-500 block mt-1">Salida: {{ $bitacora['corrida']['fecha_salida'] ?? 'Pendiente' }}</span>
                </div>
            </div>

            <div class="glass-card p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Estado de Viaje</span>
                <div class="mt-2">
                    <span class="text-base font-bold text-slate-800 block">
                        @php
                            $estado = strtolower($bitacora['corrida']['estado']);
                            $colorClass = 'text-amber-600 bg-amber-50 border-amber-200';
                            if ($estado === 'en camino') {
                                $colorClass = 'text-blue-600 bg-blue-50 border-blue-200';
                            } elseif ($estado === 'finalizado') {
                                $colorClass = 'text-emerald-600 bg-emerald-50 border-emerald-200';
                            }
                        @endphp
                        <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold rounded-md border {{ $colorClass }}">
                            {{ $bitacora['corrida']['estado'] }}
                        </span>
                    </span>
                    <span class="text-xs text-slate-500 block mt-1">Progreso en sistema</span>
                </div>
            </div>

        </div>

        <!-- Passenger List Table -->
        <div class="print-card glass-card rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800">Listado de Pasajeros</h2>
                </div>
                <span class="px-2.5 py-0.5 text-xs font-semibold bg-slate-100 text-slate-600 rounded-full">
                    {{ count($bitacora['pasajeros']) }} pasajeros
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3 px-5">Folio</th>
                            <th class="py-3 px-5">Nombre Completo</th>
                            <th class="py-3 px-5 text-center">Asiento</th>
                            <th class="py-3 px-5 text-right">Peso Equipaje</th>
                            <th class="py-3 px-5 text-center">Pago</th>
                            <th class="py-3 px-5 text-right">Descuento</th>
                            <th class="py-3 px-5 text-right">Monto Cobrado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($bitacora['pasajeros'] as $p)
                        <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                            <td class="py-3.5 px-5 font-mono text-xs text-slate-500 font-semibold">{{ $p['folio'] }}</td>
                            <td class="py-3.5 px-5 font-bold text-slate-800">{{ $p['nombre_completo'] }}</td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 font-bold text-xs bg-indigo-50 text-indigo-700 rounded-lg">
                                    {{ $p['asiento'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right text-slate-600">{{ number_format($p['peso_equipaje'], 1) }} kg</td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-slate-100 text-slate-700">
                                    {{ $p['tipo_de_pago'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right text-red-500">- ${{ number_format($p['descuento'], 2) }}</td>
                            <td class="py-3.5 px-5 text-right font-semibold text-slate-900">${{ number_format($p['total_pagado'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 font-medium">No se registraron pasajeros para este viaje.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Package List Table -->
        <div class="print-card glass-card rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800">Carga de Paquetería</h2>
                </div>
                <span class="px-2.5 py-0.5 text-xs font-semibold bg-slate-100 text-slate-600 rounded-full">
                    {{ count($bitacora['paqueteria']) }} paquetes
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3 px-5">Guía</th>
                            <th class="py-3 px-5">Descripción</th>
                            <th class="py-3 px-5 text-right">Peso</th>
                            <th class="py-3 px-5">Remitente</th>
                            <th class="py-3 px-5">Destinatario</th>
                            <th class="py-3 px-5 text-center">Pago</th>
                            <th class="py-3 px-5 text-right">Monto Cobrado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($bitacora['paqueteria'] as $pack)
                        <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                            <td class="py-3.5 px-5 font-mono text-xs text-amber-700 font-bold tracking-tight">{{ $pack['guia'] }}</td>
                            <td class="py-3.5 px-5 font-medium text-slate-700 max-w-[200px] truncate" title="{{ $pack['descripcion'] }}">{{ $pack['descripcion'] }}</td>
                            <td class="py-3.5 px-5 text-right text-slate-600 font-semibold">{{ number_format($pack['peso'], 2) }} kg</td>
                            <td class="py-3.5 px-5 text-slate-600">{{ $pack['remitente'] }}</td>
                            <td class="py-3.5 px-5 font-semibold text-slate-800">{{ $pack['destinatario'] }}</td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-slate-100 text-slate-700">
                                    {{ $pack['tipo_de_pago'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right font-semibold text-slate-900">${{ number_format($pack['total_pagado'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 font-medium">No se registraron envíos de paquetería para esta corrida.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        <div class="print-card grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="glass-card p-6 rounded-2xl shadow-sm border-l-4 border-l-indigo-500">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Efectivo por Pasajes</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-extrabold text-slate-800">${{ number_format($bitacora['resumen_financiero']['total_efectivo_boletos'], 2) }}</span>
                    <span class="text-xs font-medium text-indigo-600">MXN</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Solo ventas registradas con pago en 'Efectivo'.</p>
            </div>

            <div class="glass-card p-6 rounded-2xl shadow-sm border-l-4 border-l-amber-500">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Efectivo por Paquetería</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-extrabold text-slate-800">${{ number_format($bitacora['resumen_financiero']['total_efectivo_paqueteria'], 2) }}</span>
                    <span class="text-xs font-medium text-amber-600">MXN</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Solo envíos registrados con pago en 'Efectivo'.</p>
            </div>

            <div class="bg-slate-900 p-6 rounded-2xl shadow-lg border-l-4 border-l-emerald-500 text-white shadow-slate-200">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Efectivo a Entregar</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-black text-emerald-400">${{ number_format($bitacora['resumen_financiero']['total_efectivo_general'], 2) }}</span>
                    <span class="text-xs font-medium text-emerald-300">MXN</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Monto total en efectivo que debe entregar el chofer en caja.</p>
            </div>

        </div>

        <!-- Footer / Signature Blocks -->
        <div class="print-card grid grid-cols-2 gap-8 pt-12 mt-12 border-t border-slate-200 text-center text-xs font-semibold text-slate-500">
            <div>
                <div class="w-48 mx-auto border-b border-slate-300 h-16"></div>
                <p class="mt-3">Firma del Chofer</p>
                <p class="text-[10px] text-slate-400 font-normal mt-0.5">{{ $bitacora['corrida']['chofer'] }}</p>
            </div>
            <div>
                <div class="w-48 mx-auto border-b border-slate-300 h-16"></div>
                <p class="mt-3">Firma del Cajero / Validador</p>
                <p class="text-[10px] text-slate-400 font-normal mt-0.5">Control de Recepción</p>
            </div>
        </div>

    </div>
</body>
</html>
