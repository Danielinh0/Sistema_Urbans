<?php

namespace App\Http\Controllers;

use App\Models\BoletoCliente;
use Illuminate\Http\Request;

class BoletoClienteController extends Controller
{
    public function index()
    {
        return BoletoCliente::with(['boleto', 'asientos'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_boleto'  => 'required|exists:boletos,id|unique:boleto_clientes,id_boleto',
            'asientos'   => 'nullable|array',
            'asientos.*' => 'exists:asientos,id_asiento',
        ]);
        $boletoCliente = BoletoCliente::create(['id_boleto' => $validated['id_boleto']]);
        if (! empty($validated['asientos'])) {
            $boletoCliente->asientos()->attach($validated['asientos']);
        }
        return $boletoCliente->load('asientos');
    }

    public function show(BoletoCliente $boletoCliente)
    {
        return $boletoCliente->load(['boleto', 'asientos']);
    }

    public function destroy(BoletoCliente $boletoCliente)
    {
        $boletoCliente->delete();
        return response()->noContent();
    }
}
