<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AsientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('asiento.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('asiento.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return view('asiento.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('asiento.show', ['id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('asiento.edit', ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        return view('asiento.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return view('asiento.index');
    }
}
