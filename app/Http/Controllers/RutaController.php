<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RutaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('ruta.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ruta.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return view('ruta.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('ruta.show', ['id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('ruta.edit', ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return view('ruta.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return view('ruta.index');
    }
}
