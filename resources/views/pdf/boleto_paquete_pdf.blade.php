<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleto de Paquetería - {{ $boleto->boletoPaquete->guia }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            color: #333333;
            margin: 0;
            padding: 10px;
        }
        .ticket-box {
            border: 2px dashed #d97706;
            padding: 20px;
            width: 100%;
            max-width: 550px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #d97706;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #d97706;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #6b7280;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #d97706;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        td {
            padding: 6px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #4b5563;
            width: 35%;
        }
        .value {
            color: #111827;
        }
        .price-box {
            background-color: #fcf8f2;
            border: 1px solid #fde8d0;
            border-radius: 6px;
            padding: 12px;
            margin-top: 10px;
        }
        .total-row td {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px dashed #e5e7eb;
            padding-top: 15px;
        }
        .guia-badge {
            background-color: #d97706;
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-family: monospace;
            font-size: 14px;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="ticket-box">
        <div class="header">
            <h1>Urbans S.A.</h1>
            <p>Servicio de Transporte Ejecutivo y de Carga</p>
            <p style="font-weight: bold; color: #111827; margin-top: 5px; font-size: 14px;">BOLETO DE ENVÍO / PAQUETE</p>
        </div>

        <div class="section-title">Información de Envío</div>
        <table>
            <tr>
                <td class="label">Número de Guía:</td>
                <td class="value">
                    <span class="guia-badge">{{ $boleto->boletoPaquete->guia }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Folio Boleto:</td>
                <td class="value" style="font-family: monospace; font-size: 12px;">{{ $boleto->folio }}</td>
            </tr>
            <tr>
                <td class="label">Remitente (Cliente):</td>
                <td class="value" style="font-weight: bold;">
                    {{ trim($boleto->cliente->nombre . ' ' . $boleto->cliente->apellido_paterno . ' ' . $boleto->cliente->apellido_materno) }}
                </td>
            </tr>
            <tr>
                <td class="label">Destinatario:</td>
                <td class="value" style="font-weight: bold;">{{ $boleto->boletoPaquete->destinatario }}</td>
            </tr>
            <tr>
                <td class="label">Descripción Contenido:</td>
                <td class="value">{{ $boleto->boletoPaquete->descripcion }}</td>
            </tr>
            <tr>
                <td class="label">Peso:</td>
                <td class="value" style="font-weight: bold;">{{ number_format($boleto->boletoPaquete->peso, 2) }} kg</td>
            </tr>
        </table>

        <div class="section-title">Detalle del Transporte</div>
        <table>
            <tr>
                <td class="label">Ruta Asignada:</td>
                <td class="value" style="font-weight: bold;">{{ $boleto->corrida->ruta->nombre }}</td>
            </tr>
            <tr>
                <td class="label">Trayecto:</td>
                <td class="value">{{ $boleto->corrida->ruta->sucursalSalida->nombre }} &rarr; {{ $boleto->corrida->ruta->sucursalLlegada->nombre }}</td>
            </tr>
            <tr>
                <td class="label">Corrida / Viaje:</td>
                <td class="value">#{{ $boleto->id_corrida }}</td>
            </tr>
            <tr>
                <td class="label">Salida Programada:</td>
                <td class="value">{{ $boleto->corrida->datetime_salida ? $boleto->corrida->datetime_salida->format('d/m/Y g:i A') : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Unidad (Urban):</td>
                <td class="value">{{ $boleto->corrida->urban->codigo_urban ?? 'N/A' }}</td>
            </tr>
        </table>

        <div class="section-title">Detalle de Cobro</div>
        <div class="price-box">
            <table>
                @php
                    $tarifaBase = ($boleto->corrida->ruta->tarifa_paquete > 0) ? (float)$boleto->corrida->ruta->tarifa_paquete : (float)($boleto->corrida->ruta->tarifa_clientes ?? 0);
                    $peso = (float)$boleto->boletoPaquete->peso;
                    $extra = ($peso > 5) ? ($peso - 5) * 10 : 0;
                    $subtotal = $tarifaBase + $extra;
                    $total = max(0, $subtotal - $boleto->descuento);
                @endphp
                <tr>
                    <td class="label" style="width: 50%;">Tarifa Base Paquete:</td>
                    <td class="value" style="text-align: right;">${{ number_format($tarifaBase, 2) }}</td>
                </tr>
                @if($extra > 0)
                <tr>
                    <td class="label" style="width: 50%;">Cargo Exceso Peso (>5kg):</td>
                    <td class="value" style="text-align: right; color: #b45309;">+${{ number_format($extra, 2) }}</td>
                </tr>
                @endif
                @if($boleto->descuento > 0)
                <tr>
                    <td class="label" style="width: 50%; color: #ef4444;">Descuento:</td>
                    <td class="value" style="text-align: right; color: #ef4444;">-${{ number_format($boleto->descuento, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td style="padding-top: 10px; border-top: 1px solid #f59e0b;">Total Cobrado:</td>
                    <td style="padding-top: 10px; border-top: 1px solid #f59e0b; text-align: right; color: #d97706;">
                        ${{ number_format($total, 2) }}
                    </td>
                </tr>
            </table>
            <div style="font-size: 11px; color: #6b7280; text-align: right; margin-top: 5px;">
                Método de Pago: <strong>{{ $boleto->tipo_de_pago }}</strong>
            </div>
        </div>

        <div class="footer">
            <p>¡Gracias por confiar en Urbans Carga!</p>
            <p style="margin-top: 5px; font-size: 9px; color: #9ca3af;">Verifique que los sellos y etiquetas de empaque estén intactos al recibir.</p>
        </div>
    </div>

</body>
</html>
