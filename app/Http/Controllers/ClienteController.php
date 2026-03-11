<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        return Cliente::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'        => 'required|string|max:255',
            'peso_equipaje' => 'nullable|numeric|min:0',
        ]);
        return Cliente::create($validated);
    }

    public function show(Cliente $cliente)
    {
        return $cliente->load('boletos');
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre'        => 'sometimes|string|max:255',
            'peso_equipaje' => 'nullable|numeric|min:0',
        ]);
        $cliente->update($validated);
        return $cliente;
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return response()->noContent();
    }
}
