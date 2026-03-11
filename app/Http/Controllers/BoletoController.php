<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BoletoController extends Controller
{
    public function index()
    {
        return Boleto::with(['cliente', 'taquilla', 'corrida'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Total'        => 'required|numeric|min:0',
            'estado'       => 'required|in:activo,cancelado,usado',
            'tipo_de_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'tipo'         => 'required|in:cliente,paquete',
            'descuento'    => 'nullable|numeric|min:0',
            'guia'         => 'nullable|string|max:100',
            'id_cliente'   => 'nullable|exists:clientes,id',
            'id_taquilla'  => 'required|exists:taquillas,id',
            'id_corrida'   => 'required|exists:corridas,id',
        ]);
        $validated['folio'] = strtoupper('BOL-' . Str::random(8));
        $validated['timestamp_emision'] = now();
        return Boleto::create($validated);
    }

    public function show(Boleto $boleto)
    {
        return $boleto->load(['cliente', 'taquilla', 'corrida', 'tramos', 'boletoCliente', 'boletoPaquete']);
    }

    public function update(Request $request, Boleto $boleto)
    {
        $validated = $request->validate([
            'estado'       => 'sometimes|in:activo,cancelado,usado',
            'tipo_de_pago' => 'sometimes|in:efectivo,tarjeta,transferencia',
            'descuento'    => 'nullable|numeric|min:0',
            'guia'         => 'nullable|string|max:100',
        ]);
        $boleto->update($validated);
        return $boleto;
    }

    public function destroy(Boleto $boleto)
    {
        $boleto->delete();
        return response()->noContent();
    }
}
