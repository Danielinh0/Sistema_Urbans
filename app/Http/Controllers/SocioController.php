<?php

namespace App\Http\Controllers;

use App\Models\Socio; // Asegúrate de importar el modelo
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Importante para usar $this->authorize

class SocioController extends Controller
{
    use AuthorizesRequests; // Habilitamos el trait de autorización

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Autorización para ver el listado (Admin y Gerente)
        $this->authorize('viewAny', Socio::class);

        return view('socio.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Autorización para crear (Solo Gerente)
        $this->authorize('create', Socio::class);

        return view('socio.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Socio::class);

        // Aquí iría tu lógica de guardado

        return redirect()->route('socio.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $socio = Socio::findOrFail($id);

        // Autorización para ver un socio específico (Admin y Gerente)
        $this->authorize('view', $socio);

        return view('socio.show', compact('socio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $socio = Socio::findOrFail($id);

        // Autorización para editar (Solo Gerente)
        $this->authorize('update', $socio);

        return view('socio.edit', compact('socio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $socio = Socio::findOrFail($id);

        $this->authorize('update', $socio);

        // Aquí iría tu lógica de actualización

        return redirect()->route('socio.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $socio = Socio::findOrFail($id);

        // Autorización para eliminar (Solo Gerente)
        $this->authorize('delete', $socio);

        // Aquí iría tu lógica de eliminación

        return redirect()->route('socio.index');
    }
}
