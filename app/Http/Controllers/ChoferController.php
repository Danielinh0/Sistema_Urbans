<?php

namespace App\Http\Controllers;

use App\Models\Chofer;
use Illuminate\Http\Request;

class ChoferController extends Controller
{
    public function index()
    {
        return Chofer::with('usuario')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_usuario' => 'required|exists:usuarios,id|unique:choferes,id_usuario',
        ]);
        return Chofer::create($validated);
    }

    public function show(Chofer $chofer)
    {
        return $chofer->load('usuario');
    }

    public function destroy(Chofer $chofer)
    {
        $chofer->delete();
        return response()->noContent();
    }
}
