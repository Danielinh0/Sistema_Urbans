<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rutasAltaDemanda = Ruta::whereHas('corridas', function ($query) {
            $query->whereIn('estado', ['Programada', 'En viaje'])
                ->whereDate('datetime_salida', today());
        }, '>=', 3)->count();

        $rutasSinAsignacion = Ruta::whereDoesntHave('corridas', function ($query) {
            $query->whereDate('datetime_salida', today());
        })->count();

        $rutasMayorDuracion = Ruta::where('tiempo_estimado', '>=', '04:00:00')->count();

        $rutasMasCaras = Ruta::where('tarifa_clientes', '>=', 500)->count();

        $this->authorize('viewAny', Ruta::class);

        return view('ruta.index', compact(
            'rutasAltaDemanda',
            'rutasSinAsignacion',
            'rutasMayorDuracion',
            'rutasMasCaras'
        ));
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

        $corridasProgramadas = \App\Models\Corrida::where('id_ruta', $ruta->id_ruta)
            ->where('estado', 'Programada')
            ->where('datetime_salida', '>=', now())
            ->orderBy('datetime_salida', 'asc')
            ->count();

        $corridasFinalizadas = \App\Models\Corrida::where('id_ruta', $ruta->id_ruta)
            ->where('estado', 'Finalizada')
            ->where('datetime_salida', '<', now())
            ->orderBy('datetime_salida', 'asc')
            ->count();

        return view('ruta.show', compact('ruta', 'corridasProgramadas', 'corridasFinalizadas'));
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
