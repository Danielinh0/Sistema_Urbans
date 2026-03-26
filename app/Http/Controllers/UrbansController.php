<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Urban;

class UrbansController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $urbans = Urban::all();
        return view('urbans.index', compact('urbans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('urbans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $urban = new Urban();
        $urban->id_socio = $request->id_socio;
        $urban->placa = $request->placa;
        $urban->codigo_urban = $request->codigo_urban;
        $urban->numero_asientos = $request->numero_asientos;
        $urban->save();
        return redirect()->route('urbans.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $urban = Urban::find($id);
        return view('urbans.show', compact('urban'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $urban = Urban::find($id);
        return view('urbans.edit', compact('urban'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $urban = Urban::find($id);
        $urban->id_socio = $request->id_socio;
        $urban->placa = $request->placa;
        $urban->codigo_urban = $request->codigo_urban;
        $urban->numero_asientos = $request->numero_asientos;
        $urban->save();
        return redirect()->route('urbans.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $urban = Urban::find($id);
        $urban->delete();
        return redirect()->route('urbans.index');
    }
}
