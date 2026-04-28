<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RutaController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Ruta::class);
        return view('ruta.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Ruta::class);
        return view('ruta.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Ruta::class);

        // Tu lógica para guardar aquí...

        return redirect()->route('ruta.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ruta = Ruta::findOrFail($id);
        $this->authorize('view', $ruta);

        return view('ruta.show', ['id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $ruta = Ruta::findOrFail($id);
        $this->authorize('update', $ruta);

        return view('ruta.edit', ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ruta = Ruta::findOrFail($id);
        $this->authorize('update', $ruta);
        return redirect()->route('ruta.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ruta = Ruta::findOrFail($id);
        $this->authorize('delete', $ruta);
        return redirect()->route('ruta.index');
    }
}
