<?php

namespace App\Http\Controllers;

use App\Models\Gerente;
use Illuminate\Http\Request;

class GerenteController extends Controller
{
    public function index()
    {
        return Gerente::with('usuario')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_usuario' => 'required|exists:usuarios,id|unique:gerentes,id_usuario',
        ]);
        return Gerente::create($validated);
    }

    public function show(Gerente $gerente)
    {
        return $gerente->load('usuario');
    }

    public function destroy(Gerente $gerente)
    {
        $gerente->delete();
        return response()->noContent();
    }
}
