<?php

namespace App\Http\Controllers;

use App\Models\Taquilla;
use Illuminate\Http\Request;

class TaquillaController extends Controller
{
    public function index()
    {
        return Taquilla::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
            'monto_final'   => 'nullable|numeric|min:0',
        ]);
        return Taquilla::create($validated);
    }

    public function show(Taquilla $taquilla)
    {
        return $taquilla->load(['turnos', 'boletos']);
    }

    public function update(Request $request, Taquilla $taquilla)
    {
        $validated = $request->validate([
            'monto_inicial' => 'sometimes|numeric|min:0',
            'monto_final'   => 'nullable|numeric|min:0',
        ]);
        $taquilla->update($validated);
        return $taquilla;
    }

    public function destroy(Taquilla $taquilla)
    {
        $taquilla->delete();
        return response()->noContent();
    }
}
