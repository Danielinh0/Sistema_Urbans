<?php

namespace App\Http\Controllers;

use App\Models\Urban;
use Illuminate\Http\Request;

class UrbanController extends Controller
{
    public function index()
    {
        return Urban::with('socio')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa'           => 'required|string|unique:urbans,placa',
            'codigo_combi'    => 'required|string|unique:urbans,codigo_combi',
            'numero_asientos' => 'required|integer|min:1',
            'id_socio'        => 'required|exists:socios,id',
        ]);
        return Urban::create($validated);
    }

    public function show(Urban $urban)
    {
        return $urban->load(['socio', 'asientos', 'corridas']);
    }

    public function update(Request $request, Urban $urban)
    {
        $validated = $request->validate([
            'placa'           => 'sometimes|string|unique:urbans,placa,' . $urban->id_combi . ',id_combi',
            'codigo_combi'    => 'sometimes|string|unique:urbans,codigo_combi,' . $urban->id_combi . ',id_combi',
            'numero_asientos' => 'sometimes|integer|min:1',
            'id_socio'        => 'sometimes|exists:socios,id',
        ]);
        $urban->update($validated);
        return $urban;
    }

    public function destroy(Urban $urban)
    {
        $urban->delete();
        return response()->noContent();
    }
}
