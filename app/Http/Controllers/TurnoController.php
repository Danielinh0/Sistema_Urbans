<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turno;

class TurnoController extends Controller
{
    public function close(Request $request)
    {
        $user = $request->user();

        $turno = Turno::where('id_usuario', $user->id_usuario)
            ->whereNull('hora_fin')
            ->latest('hora_inicio')
            ->first();

        if (!$turno) {
            return redirect()->back()
                ->with('warning', 'No tienes un turno activo para cerrar.');
        }

        $turno->update([
            'hora_fin' => now()->toTimeString()  // Consistente con tu hora_inicio
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Turno cerrado correctamente.');
    }
}
