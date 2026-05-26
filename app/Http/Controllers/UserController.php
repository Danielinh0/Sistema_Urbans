<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuariosEnTurno = User::whereHas('turnoActivo')->count();

        $administradoresYGerentes = User::role(['admin', 'gerente'])->count();

        $usuariosSinTurno = User::role(['cajero'])
            ->whereDoesntHave('turnos', function ($query) {
                $query->whereDate('fecha', today());
            })->count();

        $conductoresHoy = User::whereHas('corridas', function ($query) {
            $query->whereIn('estado', ['Programada', 'En viaje'])
                ->whereDate('datetime_salida', today());
        })
            ->distinct('id_usuario')
            ->count();

        return view('usuario.index', compact(
            'usuariosEnTurno',
            'administradoresYGerentes',
            'usuariosSinTurno',
            'conductoresHoy'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('usuario.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return view('usuario.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('usuario.show', ['id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('usuario.edit', ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        return view('usuario.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return view('usuario.index');
    }
}
