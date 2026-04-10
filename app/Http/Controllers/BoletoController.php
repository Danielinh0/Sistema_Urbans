<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BoletoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('boleto.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('boleto.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return view('boleto.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('boleto.show', ['id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('boleto.edit', ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        return view('boleto.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return view('boleto.index');
    }
}
