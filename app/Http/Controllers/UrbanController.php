<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Urban;
use App\Models\Corrida;

class UrbanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   

        $UrbansActivas = Urban::where('estado', 'Activa')->count(); 
        $UrbansInactivas = Urban::where('estado', 'Inactiva')->count();   
        $UrbansFueraDeServicio = Urban::where('estado', 'Fuera de servicio')->count();   
        $UrbansMantenimiento = Urban::where('estado', 'Mantenimiento')->count();   


        return view('urban.index', compact('UrbansActivas', 'UrbansInactivas', 'UrbansFueraDeServicio', 'UrbansMantenimiento'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return view('urban.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {   
        $urban = Urban::findOrFail($id);

        $corridasProgramadas = Corrida::where('id_urban', $urban->id_urban)
            ->where('estado', 'Programada')
            ->where('datetime_salida', '>=', now())
            ->orderBy('datetime_salida', 'asc')
            ->count();

        $corridasFinalizadas = Corrida::where('id_urban', $urban->id_urban)
            ->where('estado', 'Finalizada')
            ->where('datetime_salida', '<', now())
            ->orderBy('datetime_salida', 'asc')
            ->count();

        return view('urban.show', compact('urban', 'corridasProgramadas', 'corridasFinalizadas'));
        // return view('urban.show', ['id' => $id]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        return view('urban.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return view('urban.index');
    }
}
