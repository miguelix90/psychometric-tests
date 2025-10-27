<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class ParticipantSession
{
    /**
     * Handle an incoming request.
     *
     * Verifica que exista una sesión activa de participante
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si existe una sesión de participante
        if (!Session::has('participant_id')) {
            return redirect()->route('welcome')
                ->withErrors(['session' => 'Debes acceder con tu código de participante.']);
        }

        // Verificar que la sesión no haya expirado (opcional: 2 horas)
        $accessTime = Session::get('participant_access_time');
        if ($accessTime && now()->diffInHours($accessTime) > 2) {
            Session::forget('participant_id');
            Session::forget('participant_iuc');
            Session::forget('institution_id');
            Session::forget('participant_access_time');

            return redirect()->route('welcome')
                ->withErrors(['session' => 'Tu sesión ha expirado por inactividad.']);
        }

        // Actualizar el tiempo de último acceso
        Session::put('participant_access_time', now());

        return $next($request);
    }
}
