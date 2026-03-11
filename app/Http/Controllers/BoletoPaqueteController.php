<?php

namespace App\Http\Controllers;

use App\Models\BoletoPaquete;
use Illuminate\Http\Request;

class BoletoPaqueteController extends Controller
{
    public function index()
    {
        return BoletoPaquete::with(['boleto', 'paquete'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_boleto' => 'required|exists:boletos,id|unique:boleto_paquetes,id_boleto',
        ]);
        return BoletoPaquete::create($validated);
    }

    public function show(BoletoPaquete $boletoPaquete)
    {
        return $boletoPaquete->load(['boleto', 'paquete']);
    }

    public function destroy(BoletoPaquete $boletoPaquete)
    {
        $boletoPaquete->delete();
        return response()->noContent();
    }
}
