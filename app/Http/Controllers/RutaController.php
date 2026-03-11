<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    public function index()
    {
        return Ruta::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'distancia' => 'required|numeric|min:0',
        ]);
        return Ruta::create($validated);
    }

    public function show(Ruta $ruta)
    {
        return $ruta->load('tramos');
    }

    public function update(Request $request, Ruta $ruta)
    {
        $validated = $request->validate([
            'nombre'    => 'sometimes|string|max:255',
            'distancia' => 'sometimes|numeric|min:0',
        ]);
        $ruta->update($validated);
        return $ruta;
    }

    public function destroy(Ruta $ruta)
    {
        $ruta->delete();
        return response()->noContent();
    }
}
