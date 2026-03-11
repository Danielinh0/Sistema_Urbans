<?php

namespace App\Http\Controllers;

use App\Models\Asiento;
use Illuminate\Http\Request;

class AsientoController extends Controller
{
    public function index()
    {
        return Asiento::with('urban')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'   => 'required|string|max:50',
            'id_combi' => 'required|exists:urbans,id_combi',
        ]);
        return Asiento::create($validated);
    }

    public function show(Asiento $asiento)
    {
        return $asiento->load('urban');
    }

    public function update(Request $request, Asiento $asiento)
    {
        $validated = $request->validate([
            'nombre'   => 'sometimes|string|max:50',
            'id_combi' => 'sometimes|exists:urbans,id_combi',
        ]);
        $asiento->update($validated);
        return $asiento;
    }

    public function destroy(Asiento $asiento)
    {
        $asiento->delete();
        return response()->noContent();
    }
}
