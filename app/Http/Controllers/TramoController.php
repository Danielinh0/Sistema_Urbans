<?php

namespace App\Http\Controllers;

use App\Models\Tramo;
use Illuminate\Http\Request;

class TramoController extends Controller
{
    public function index()
    {
        return Tramo::with(['ruta', 'paradaSale', 'paradaLlega'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tarifa_cliente'  => 'required|numeric|min:0',
            'tarifa_paquete'  => 'required|numeric|min:0',
            'id_ruta'         => 'required|exists:rutas,id',
            'id_parada_sale'  => 'required|exists:paradas,id',
            'id_parada_llega' => 'required|exists:paradas,id|different:id_parada_sale',
        ]);
        return Tramo::create($validated);
    }

    public function show(Tramo $tramo)
    {
        return $tramo->load(['ruta', 'paradaSale', 'paradaLlega', 'corridas']);
    }

    public function update(Request $request, Tramo $tramo)
    {
        $validated = $request->validate([
            'tarifa_cliente'  => 'sometimes|numeric|min:0',
            'tarifa_paquete'  => 'sometimes|numeric|min:0',
            'id_ruta'         => 'sometimes|exists:rutas,id',
            'id_parada_sale'  => 'sometimes|exists:paradas,id',
            'id_parada_llega' => 'sometimes|exists:paradas,id',
        ]);
        $tramo->update($validated);
        return $tramo;
    }

    public function destroy(Tramo $tramo)
    {
        $tramo->delete();
        return response()->noContent();
    }
}
