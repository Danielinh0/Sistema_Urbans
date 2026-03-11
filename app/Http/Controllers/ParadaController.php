<?php

namespace App\Http\Controllers;

use App\Models\Parada;
use Illuminate\Http\Request;

class ParadaController extends Controller
{
    public function index()
    {
        return Parada::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
        ]);
        return Parada::create($validated);
    }

    public function show(Parada $parada)
    {
        return $parada;
    }

    public function update(Request $request, Parada $parada)
    {
        $validated = $request->validate([
            'nombre'    => 'sometimes|string|max:255',
            'direccion' => 'sometimes|string|max:255',
        ]);
        $parada->update($validated);
        return $parada;
    }

    public function destroy(Parada $parada)
    {
        $parada->delete();
        return response()->noContent();
    }
}
