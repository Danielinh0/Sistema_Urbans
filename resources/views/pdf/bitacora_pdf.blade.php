<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bitácora de Viaje - Corrida #{{ $bitacora['corrida']['id_corrida'] }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 5px;
        }
        .header {
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-title h1 {
            margin: 0;
            font-size: 20px;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title p {
            margin: 3px 0 0 0;
            font-size: 11px;
            color: #64748b;
        }
        .header-right {
            text-align: right;
        }
        .header-right .urban-badge {
            background-color: #4f46e5;
            color: #ffffff;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
            font-size: 12px;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 15px;
            margin-bottom: 8px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .meta-table td {
            padding: 8px 12px;
            vertical-align: top;
            width: 25%;
        }
        .meta-label {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .meta-value {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 8px;
            border-bottom: 2px solid #cbd5e1;
            text-align: left;
        }
        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .asiento-badge {
            background-color: #e0e7ff;
            color: #4338ca;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
        }
        .guia-text {
            color: #b45309;
            font-weight: bold;
            font-family: monospace;
        }
        .financial-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .financial-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            border-radius: 6px;
            width: 30%;
            vertical-align: top;
        }
        .financial-box.total {
            background-color: #0f172a;
            color: #ffffff;
            border: 1px solid #0f172a;
        }
        .financial-title {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .financial-box.total .financial-title {
            color: #94a3b8;
        }
        .financial-amount {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }
        .financial-box.total .financial-amount {
            color: #34d399;
        }
        .footer-signatures {
            margin-top: 50px;
            width: 100%;
            border-collapse: collapse;
        }
        .footer-signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .signature-line {
            width: 180px;
            border-bottom: 1px solid #cbd5e1;
            margin: 0 auto 5px auto;
            height: 40px;
        }
        .signature-title {
            font-size: 10px;
            font-weight: bold;
            color: #475569;
        }
        .signature-subtitle {
            font-size: 8px;
            color: #64748b;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-title">
                    <h1>Urbans S.A.</h1>
                    <p>Servicio de Transporte Ejecutivo y de Carga</p>
                    <p style="font-weight: bold; color: #0f172a; margin-top: 3px; font-size: 12px; text-transform: uppercase;">
                        Bitácora y Manifiesto de Corrida Oficial
                    </p>
                </td>
                <td class="header-right" style="vertical-align: middle;">
                    <div style="font-size: 9px; color: #64748b; margin-bottom: 4px;">UNIDAD (URBAN)</div>
                    <span class="urban-badge">{{ $bitacora['corrida']['unidad_urban'] }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Metadata Section -->
    <table class="meta-table">
        <tr>
            <td>
                <div class="meta-label">Corrida</div>
                <div class="meta-value">#{{ $bitacora['corrida']['id_corrida'] }}</div>
            </td>
            <td>
                <div class="meta-label">Ruta / Trayecto</div>
                <div class="meta-value" style="font-size: 10px;">{{ $bitacora['corrida']['ruta'] }}</div>
                <div style="font-size: 8px; color: #64748b; margin-top: 2px;">
                    {{ $bitacora['corrida']['origen'] }} &rarr; {{ $bitacora['corrida']['destino'] }}
                </div>
            </td>
            <td>
                <div class="meta-label">Chofer Operador</div>
                <div class="meta-value">{{ $bitacora['corrida']['chofer'] }}</div>
            </td>
            <td>
                <div class="meta-label">Fecha y Hora Salida</div>
                <div class="meta-value">{{ $bitacora['corrida']['hora_salida'] }}</div>
                <div style="font-size: 8px; color: #64748b; margin-top: 2px;">
                    Fecha: {{ $bitacora['corrida']['fecha_salida'] ?? 'N/A' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Passengers Section -->
    <div class="section-title">Listado de Pasajeros ({{ count($bitacora['pasajeros']) }} Registrados)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Folio</th>
                <th style="width: 35%;">Nombre del Pasajero</th>
                <th class="text-center" style="width: 10%;">Asiento</th>
                <th class="text-right" style="width: 12%;">Equipaje</th>
                <th class="text-center" style="width: 10%;">Pago</th>
                <th class="text-right" style="width: 18%;">Neto Cobrado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bitacora['pasajeros'] as $p)
            <tr>
                <td style="font-family: monospace; font-size: 9px; font-weight: bold;">{{ $p['folio'] }}</td>
                <td style="font-weight: bold; color: #0f172a;">{{ $p['nombre_completo'] }}</td>
                <td class="text-center">
                    <span class="asiento-badge">{{ $p['asiento'] }}</span>
                </td>
                <td class="text-right">{{ number_format($p['peso_equipaje'], 1) }} kg</td>
                <td class="text-center">{{ $p['tipo_de_pago'] }}</td>
                <td class="text-right" style="font-weight: bold; color: #0f172a;">
                    @if($p['descuento'] > 0)
                        <span style="font-size: 8px; color: #64748b; font-weight: normal; margin-right: 3px;">(Desc. -${{ number_format($p['descuento'], 0) }})</span>
                    @endif
                    ${{ number_format($p['total_pagado'], 2) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="color: #64748b; padding: 12px;">No hay pasajeros registrados en esta corrida.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Packages Section -->
    <div class="section-title">Carga de Paquetería ({{ count($bitacora['paqueteria']) }} Envíos)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 18%;">Número Guía</th>
                <th style="width: 25%;">Descripción</th>
                <th class="text-right" style="width: 10%;">Peso</th>
                <th style="width: 18%;">Remitente</th>
                <th style="width: 18%;">Destinatario</th>
                <th class="text-center" style="width: 11%;">Neto Cobrado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bitacora['paqueteria'] as $pack)
            <tr>
                <td class="guia-text">{{ $pack['guia'] }}</td>
                <td>{{ $pack['descripcion'] }}</td>
                <td class="text-right" style="font-weight: bold;">{{ number_format($pack['peso'], 2) }} kg</td>
                <td style="font-size: 9px; color: #475569;">{{ $pack['remitente'] }}</td>
                <td style="font-weight: bold;">{{ $pack['destinatario'] }}</td>
                <td class="text-right" style="font-weight: bold; color: #0f172a;">
                    ${{ number_format($pack['total_pagado'], 2) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="color: #64748b; padding: 12px;">No hay envíos de paquetería registrados en esta corrida.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Financial Summary -->
    <div class="section-title">Resumen Financiero de Caja</div>
    <table class="financial-table">
        <tr>
            <td class="financial-box">
                <div class="financial-title">Efectivo por Pasajes</div>
                <div class="financial-amount">${{ number_format($bitacora['resumen_financiero']['total_efectivo_boletos'], 2) }} <span style="font-size: 9px; font-weight: normal; color: #64748b;">MXN</span></div>
            </td>
            <td style="width: 5%;"></td>
            <td class="financial-box">
                <div class="financial-title">Efectivo por Paquetería</div>
                <div class="financial-amount">${{ number_format($bitacora['resumen_financiero']['total_efectivo_paqueteria'], 2) }} <span style="font-size: 9px; font-weight: normal; color: #64748b;">MXN</span></div>
            </td>
            <td style="width: 5%;"></td>
            <td class="financial-box total">
                <div class="financial-title">Total Efectivo a Entregar</div>
                <div class="financial-amount">${{ number_format($bitacora['resumen_financiero']['total_efectivo_general'], 2) }} <span style="font-size: 9px; font-weight: normal; color: #a7f3d0;">MXN</span></div>
            </td>
        </tr>
    </table>

    <!-- Signatures -->
    <table class="footer-signatures">
        <tr>
            <td>
                <div class="signature-line"></div>
                <div class="signature-title">Firma del Chofer Operador</div>
                <div class="signature-subtitle">{{ $bitacora['corrida']['chofer'] }}</div>
            </td>
            <td>
                <div class="signature-line"></div>
                <div class="signature-title">Firma del Cajero / Validador</div>
                <div class="signature-subtitle">Control de Recepción de Caja</div>
            </td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 40px; font-size: 8px; color: #94a3b8; border-top: 1px dashed #cbd5e1; padding-top: 10px;">
        Este manifiesto es un documento oficial interno de Urbans S.A. Cualquier alteración es motivo de sanción.
    </div>

</body>
</html>
