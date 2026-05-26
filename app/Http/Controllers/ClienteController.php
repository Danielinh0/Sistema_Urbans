<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Cliente;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalClientes = Cliente::count();

        $clientesFrecuentes = Cliente::has('boletos', '>=', 3)->count();

        $clientesHoy = Cliente::whereHas('boletos.corrida', function ($query) {
            $query->whereIn('estado', ['Programada', 'En viaje'])
                ->whereDate('datetime_salida', today());
        })
            ->distinct('id_cliente')
            ->count();

        $clientesNuevosMes = Cliente::whereHas('ventas', function ($query) {

            $query->whereMonth('fecha', Carbon::now()->month)
                ->whereYear('fecha', Carbon::now()->year);
        })
            ->whereDoesntHave('ventas', function ($query) {
                $query->where('fecha', '<', Carbon::now()->startOfMonth());
            })
            ->count();

        return view('cliente.index', compact(
            'totalClientes',
            'clientesFrecuentes',
            'clientesHoy',
            'clientesNuevosMes'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cliente.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return view('cliente.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('cliente.show', ['id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('cliente.edit', ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        return view('cliente.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return view('cliente.index');
    }
}
