<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use Illuminate\Http\Request;

class SocioController extends Controller
{
    public function index()
    {
        return Socio::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_telefonico' => 'required|string|max:20',
            'correo'            => 'required|email|unique:socios,correo',
        ]);
        return Socio::create($validated);
    }

    public function show(Socio $socio)
    {
        return $socio->load('urbans');
    }

    public function update(Request $request, Socio $socio)
    {
        $validated = $request->validate([
            'numero_telefonico' => 'sometimes|string|max:20',
            'correo'            => 'sometimes|email|unique:socios,correo,' . $socio->id,
        ]);
        $socio->update($validated);
        return $socio;
    }

    public function destroy(Socio $socio)
    {
        $socio->delete();
        return response()->noContent();
    }
}
