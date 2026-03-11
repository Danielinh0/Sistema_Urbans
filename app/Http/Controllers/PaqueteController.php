<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use Illuminate\Http\Request;

class PaqueteController extends Controller
{
    public function index()
    {
        return Paquete::with('boletoPaquete')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'peso'         => 'required|numeric|min:0.1',
            'descripcion'  => 'required|string|max:255',
            'tipo_de_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'destinatario' => 'required|string|max:255',
            'id_boleto'    => 'required|exists:boleto_paquetes,id_boleto',
        ]);
        return Paquete::create($validated);
    }

    public function show(Paquete $paquete)
    {
        return $paquete->load('boletoPaquete.boleto');
    }

    public function update(Request $request, Paquete $paquete)
    {
        $validated = $request->validate([
            'peso'         => 'sometimes|numeric|min:0.1',
            'descripcion'  => 'sometimes|string|max:255',
            'tipo_de_pago' => 'sometimes|in:efectivo,tarjeta,transferencia',
            'destinatario' => 'sometimes|string|max:255',
        ]);
        $paquete->update($validated);
        return $paquete;
    }

    public function destroy(Paquete $paquete)
    {
        $paquete->delete();
        return response()->noContent();
    }
}
