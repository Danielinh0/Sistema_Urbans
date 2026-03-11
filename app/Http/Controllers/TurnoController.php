<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Http\Request;

class TurnoController extends Controller
{
    public function index()
    {
        return Turno::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha'   => 'required|date',
            'horario' => 'required|string|in:Matutino,Vespertino,Nocturno',
        ]);
        return Turno::create($validated);
    }

    public function show(Turno $turno)
    {
        return $turno->load(['corridas', 'taquillas', 'boletos']);
    }

    public function update(Request $request, Turno $turno)
    {
        $validated = $request->validate([
            'fecha'   => 'sometimes|date',
            'horario' => 'sometimes|string|in:Matutino,Vespertino,Nocturno',
        ]);
        $turno->update($validated);
        return $turno;
    }

    public function destroy(Turno $turno)
    {
        $turno->delete();
        return response()->noContent();
    }
}
