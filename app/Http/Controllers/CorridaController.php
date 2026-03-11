<?php

namespace App\Http\Controllers;

use App\Models\Corrida;
use Illuminate\Http\Request;

class CorridaController extends Controller
{
    public function index()
    {
        return Corrida::with('urban')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Hora_salida'  => 'required|date_format:H:i:s',
            'Hora_llegada' => 'required|date_format:H:i:s',
            'Fecha'        => 'required|date',
            'id_urban'     => 'required|exists:urbans,id_combi',
        ]);
        return Corrida::create($validated);
    }

    public function show(Corrida $corrida)
    {
        return $corrida->load(['urban', 'tramos', 'turnos', 'boletos']);
    }

    public function update(Request $request, Corrida $corrida)
    {
        $validated = $request->validate([
            'Hora_salida'  => 'sometimes|date_format:H:i:s',
            'Hora_llegada' => 'sometimes|date_format:H:i:s',
            'Fecha'        => 'sometimes|date',
            'id_urban'     => 'sometimes|exists:urbans,id_combi',
        ]);
        $corrida->update($validated);
        return $corrida;
    }

    public function destroy(Corrida $corrida)
    {
        $corrida->delete();
        return response()->noContent();
    }
}
