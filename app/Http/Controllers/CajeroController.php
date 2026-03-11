<?php

namespace App\Http\Controllers;

use App\Models\Cajero;
use Illuminate\Http\Request;

class CajeroController extends Controller
{
    public function index()
    {
        return Cajero::with('usuario')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_usuario' => 'required|exists:usuarios,id|unique:cajeros,id_usuario',
        ]);
        return Cajero::create($validated);
    }

    public function show(Cajero $cajero)
    {
        return $cajero->load('usuario');
    }

    public function destroy(Cajero $cajero)
    {
        $cajero->delete();
        return response()->noContent();
    }
}
