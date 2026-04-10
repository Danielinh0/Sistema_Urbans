<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('socio.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('socio.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return view('socio.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('socio.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('socio.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        return view('socio.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return view('socio.index');
    }
}
