<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index()
    {
        return Sucursal::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
        ]);
        return Sucursal::create($validated);
    }

    public function show(Sucursal $sucursal)
    {
        return $sucursal->load('usuarios');
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $validated = $request->validate([
            'nombre'    => 'sometimes|string|max:255',
            'ubicacion' => 'sometimes|string|max:255',
        ]);
        $sucursal->update($validated);
        return $sucursal;
    }

    public function destroy(Sucursal $sucursal)
    {
        $sucursal->delete();
        return response()->noContent();
    }
}
