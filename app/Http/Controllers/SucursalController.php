<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sucursal;

class SucursalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalSucursales = Sucursal::count();

        $sucursalesSinRutasSalida = Sucursal::whereDoesntHave('rutas', function ($query) {})->whereNotIn('id_sucursal', function ($query) {
            $query->select('id_sucursal_salida')->from('ruta');
        })->count();


        $sucursalesCompartidas = Sucursal::whereIn('id_direccion', function ($query) {
            $query->select('id_direccion')
                ->from('sucursal')
                ->whereNotNull('id_direccion')
                ->groupBy('id_direccion')
                ->havingRaw('COUNT(*) > 1');
        })->count();

        $sucursalesAisladas = Sucursal::whereDoesntHave('rutas')->count();

        return view('sucursal.index', compact(
            'totalSucursales',
            'sucursalesSinRutasSalida',
            'sucursalesCompartidas',
            'sucursalesAisladas'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sucursal.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('sucursal.show', ['id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('sucursal.edit', ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return view('sucursal.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return view('sucursal.index');
    }
}
