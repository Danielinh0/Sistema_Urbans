<?php

namespace App\Http\Middleware;

use App\Models\Turno;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureTurnoOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $hayTurnoActivo = true;

        // Solo aplicamos la restricción a cajeros
        if ($user && $user->hasRole('cajero')) {
            $hayTurnoActivo = Turno::where('id_usuario', $user->id_usuario)
                ->whereNull('hora_fin')
                ->exists();
        }

        View::share('hayTurnoActivo', $hayTurnoActivo);

        return $next($request);
    }
}
