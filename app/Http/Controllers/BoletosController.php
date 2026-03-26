<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boleto;

class BoletosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boletos = Boleto::all();
        return view('boletos.index', compact('boletos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('boletos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $boleto = new Boleto();
        $boleto->id_corrida = $request->id_corrida;
        $boleto->id_turno = $request->id_turno;
        $boleto->id_cliente = $request->id_cliente;
        $boleto->folio = $request->folio;
        $boleto->estado = $request->estado;
        $boleto->tipo_de_pago = $request->tipo_de_pago;
        $boleto->descuento = $request->descuento;
        $boleto->save();
        return redirect()->route('boletos.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $boleto = Boleto::find($id);
        return view('boletos.show', compact('boleto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $boleto = Boleto::find($id);
        return view('boletos.edit', compact('boleto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $boleto = Boleto::find($id);
        $boleto->id_corrida = $request->id_corrida;
        $boleto->id_turno = $request->id_turno;
        $boleto->id_cliente = $request->id_cliente;
        $boleto->folio = $request->folio;
        $boleto->estado = $request->estado;
        $boleto->tipo_de_pago = $request->tipo_de_pago;
        $boleto->descuento = $request->descuento;
        $boleto->save();
        return redirect()->route('boletos.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $boleto = Boleto::find($id);
        $boleto->delete();
        return redirect()->route('boletos.index');
    }
}
