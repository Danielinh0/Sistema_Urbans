<?php

namespace App\Http\Controllers;

use App\Models\Corrida;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $corridas = Corrida::with(['ruta', 'urban', 'boletos'])
            ->whereDate('fecha', today())
            ->orderBy('hora_salida', 'asc')
            ->get()
            ->map(function ($corrida) {
                $totalAsientos    = $corrida->urban?->numero_asientos ?? 0;
                $boletosVendidos  = $corrida->boletos->count();
                $asientosLibres   = max(0, $totalAsientos - $boletosVendidos);

                $horaFormateada = $corrida->hora_salida
                    ? \Carbon\Carbon::parse($corrida->hora_salida)->format('g:i A')
                    : 'N/A';

                return [
                    'id'              => $corrida->id_corrida,
                    'hora_salida'     => $horaFormateada,
                    'ruta'            => $corrida->ruta?->nombre ?? 'Sin ruta asignada',
                    'codigo_urban'    => $corrida->urban?->codigo_urban ?? '—',
                    'asientos_libres' => $asientosLibres,
                    'total_asientos'  => $totalAsientos,
                    'lleno'           => $totalAsientos > 0 && $asientosLibres === 0,
                ];
            });

        return view('dashboard', [
            'corridas'  => $corridas,
            'fecha_hoy' => Carbon::now()->locale('es')->isoFormat('ddd D [de] MMMM, YYYY'),
        ]);
    }
}
