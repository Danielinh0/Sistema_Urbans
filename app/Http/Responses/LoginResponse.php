<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();
        $home = '/dashboard';

        if ($user->hasRole('cajero')) {
            $turnoAbierto = \App\Models\Turno::where('id_usuario', $user->id_usuario)
                ->whereNull('hora_fin')
                ->exists();

            if (!$turnoAbierto) {
                $home = route('turno.create');
            }
        }

        return $request->wantsJson()
            ? response()->json(['two_factor' => false, 'redirect' => $home])
            : redirect()->intended($home);
    }
}
