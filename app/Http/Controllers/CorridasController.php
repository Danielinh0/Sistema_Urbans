<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Corrida;

class CorridasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $corridas = Corrida::all();
        return view('corridas.index', compact('corridas'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('corridas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $corrida = new Corrida();
        $corrida->id_ruta = $request->id_ruta;
        $corrida->id_combi = $request->id_combi;
        $corrida->id_usuario = $request->id_usuario;
        $corrida->fecha = $request->fecha;
        $corrida->hora_salida = $request->hora_salida;
        $corrida->hora_llegada = $request->hora_llegada;
        $corrida->save();
        return redirect()->route('corridas.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $corrida = Corrida::find($id);
        return view('corridas.show', compact('corrida'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $corrida = Corrida::find($id);
        return view('corridas.edit', compact('corrida'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $corrida = Corrida::find($id);
        $corrida->id_ruta = $request->id_ruta;
        $corrida->id_combi = $request->id_combi;
        $corrida->id_usuario = $request->id_usuario;
        $corrida->fecha = $request->fecha;
        $corrida->hora_salida = $request->hora_salida;
        $corrida->hora_llegada = $request->hora_llegada;
        $corrida->save();
        return redirect()->route('corridas.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $corrida = Corrida::find($id);
        $corrida->delete();
        return redirect()->route('corridas.index');
    }
}
