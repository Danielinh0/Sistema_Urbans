<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        return Usuario::with('sucursal')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255',
            'password'    => 'required|string|min:8',
            'correo'      => 'required|email|unique:usuarios,correo',
            'direccion'   => 'nullable|string|max:255',
            'id_sucursal' => 'required|exists:sucursales,id',
        ]);
        $validated['password'] = bcrypt($validated['password']);
        return Usuario::create($validated);
    }

    public function show(Usuario $usuario)
    {
        return $usuario->load(['sucursal', 'gerente', 'chofer', 'cajero']);
    }

    public function update(Request $request, Usuario $usuario)
    {
        $validated = $request->validate([
            'nombre'      => 'sometimes|string|max:255',
            'password'    => 'sometimes|string|min:8',
            'correo'      => 'sometimes|email|unique:usuarios,correo,' . $usuario->id,
            'direccion'   => 'nullable|string|max:255',
            'id_sucursal' => 'sometimes|exists:sucursales,id',
        ]);
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }
        $usuario->update($validated);
        return $usuario;
    }

    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return response()->noContent();
    }
}
