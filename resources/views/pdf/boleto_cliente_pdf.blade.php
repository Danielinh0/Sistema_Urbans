<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleto de Pasajero - {{ $boleto->folio }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            color: #333333;
            margin: 0;
            padding: 10px;
        }
        .ticket-box {
            border: 2px dashed #4f46e5;
            padding: 20px;
            width: 100%;
            max-width: 550px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #4f46e5;
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
            color: #4f46e5;
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
            background-color: #f3f4f6;
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
        .seat-badge {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="ticket-box">
        <div class="header">
            <h1>Urbans S.A.</h1>
            <p>Servicio de Transporte Ejecutivo y de Carga</p>
            <p style="font-weight: bold; color: #111827; margin-top: 5px; font-size: 14px;">BOLETO DE CLIENTE</p>
        </div>

        <div class="section-title">Detalles del Pasaje</div>
        <table>
            <tr>
                <td class="label">Folio de Boleto:</td>
                <td class="value" style="font-weight: bold; font-family: monospace; font-size: 14px;">{{ $boleto->folio }}</td>
            </tr>
            <tr>
                <td class="label">Pasajero:</td>
                <td class="value" style="font-weight: bold;">
                    {{ trim($boleto->cliente->nombre . ' ' . $boleto->cliente->apellido_paterno . ' ' . $boleto->cliente->apellido_materno) }}
                </td>
            </tr>
            <tr>
                <td class="label">Asiento Asignado:</td>
                <td class="value">
                    <span class="seat-badge">{{ $boleto->boletoCliente->asiento->nombre ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Peso de Equipaje:</td>
                <td class="value">{{ number_format($boleto->boletoCliente->peso_equipaje, 1) }} kg</td>
            </tr>
        </table>

        <div class="section-title">Información del Viaje</div>
        <table>
            <tr>
                <td class="label">Corrida:</td>
                <td class="value">#{{ $boleto->id_corrida }}</td>
            </tr>
            <tr>
                <td class="label">Ruta:</td>
                <td class="value" style="font-weight: bold;">{{ $boleto->corrida->ruta->nombre }}</td>
            </tr>
            <tr>
                <td class="label">Trayecto:</td>
                <td class="value">{{ $boleto->corrida->ruta->sucursalSalida->nombre }} &rarr; {{ $boleto->corrida->ruta->sucursalLlegada->nombre }}</td>
            </tr>
            <tr>
                <td class="label">Fecha & Hora Salida:</td>
                <td class="value">{{ $boleto->corrida->datetime_salida ? $boleto->corrida->datetime_salida->format('d/m/Y g:i A') : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Unidad (Urban):</td>
                <td class="value">{{ $boleto->corrida->urban->codigo_urban ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Chofer:</td>
                <td class="value">
                    @if($boleto->corrida->user)
                        {{ trim($boleto->corrida->user->name . ' ' . $boleto->corrida->user->apellido_paterno . ' ' . $boleto->corrida->user->apellido_materno) }}
                    @else
                        Sin asignar
                    @endif
                </td>
            </tr>
        </table>

        <div class="section-title">Detalle de Cobro</div>
        <div class="price-box">
            <table>
                <tr>
                    <td class="label" style="width: 50%;">Tarifa del Viaje:</td>
                    <td class="value" style="text-align: right;">${{ number_format($boleto->corrida->ruta->tarifa_clientes, 2) }}</td>
                </tr>
                @if($boleto->descuento > 0)
                <tr>
                    <td class="label" style="width: 50%; color: #ef4444;">Descuento Aplicado:</td>
                    <td class="value" style="text-align: right; color: #ef4444;">-${{ number_format($boleto->descuento, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td style="padding-top: 10px; border-top: 1px solid #d1d5db;">Monto Pagado:</td>
                    <td style="padding-top: 10px; border-top: 1px solid #d1d5db; text-align: right; color: #10b981;">
                        @php
                            $total = max(0, $boleto->corrida->ruta->tarifa_clientes - $boleto->descuento);
                        @endphp
                        ${{ number_format($total, 2) }}
                    </td>
                </tr>
            </table>
            <div style="font-size: 11px; color: #6b7280; text-align: right; margin-top: 5px;">
                Método de Pago: <strong>{{ $boleto->tipo_de_pago }}</strong>
            </div>
        </div>

        <div class="footer">
            <p>¡Gracias por viajar con nosotros!</p>
            <p style="margin-top: 5px; font-size: 9px; color: #9ca3af;">Conserve este boleto para cualquier aclaración o seguro de viaje.</p>
        </div>
    </div>

</body>
</html>
