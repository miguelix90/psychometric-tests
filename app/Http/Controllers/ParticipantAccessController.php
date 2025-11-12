<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\TestSession;

class ParticipantAccessController extends Controller
{
    /**
     * Mostrar el formulario de acceso con el código de institución
     */
    public function showAccessForm($access_code)
    {
        // Validar que el access_code existe
        $institution = Institution::where('access_code', $access_code)->first();

        if (!$institution) {
            abort(404, 'Código de acceso inválido.');
        }

        // Verificar que la institución tiene usos disponibles
        if ($institution->available_uses <= 0) {
            return view('participant.no-uses', compact('institution'));
        }

        return view('participant.access-form', compact('institution', 'access_code'));
    }

    /**
     * Validar los datos personales y dar acceso al participante
     */
   public function validateAccess(Request $request, $access_code)
{
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'birth_date' => 'required|date|before:today',
        'sex' => 'required|in:M,F,O',
    ], [
        'first_name.required' => 'El nombre es obligatorio.',
        'last_name.required' => 'El apellido es obligatorio.',
        'birth_date.required' => 'La fecha de nacimiento es obligatoria.',
        'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        'sex.required' => 'El sexo es obligatorio.',
    ]);

    // Buscar la institución por access_code
    $institution = Institution::where('access_code', $access_code)->first();

    if (!$institution) {
        return back()->withErrors(['error' => 'Código de institución no válido.'])->withInput();
    }

    // Verificar que la institución tiene usos disponibles
    if ($institution->available_uses <= 0) {
        return view('participant.no-uses', compact('institution'));
    }

    // Generar el IUC con los datos proporcionados
    $iuc = Participant::generateIUC(
        $access_code,
        $request->first_name,
        $request->last_name,
        $request->birth_date
    );

    // Buscar el participante por IUC
    $participant = Participant::where('iuc', $iuc)
        ->where('institution_id', $institution->id)
        ->where('sex', $request->sex)
        ->first();

    if (!$participant) {
        return back()->withErrors([
            'error' => 'No se encontró un participante con los datos proporcionados. Verifica que tu nombre, apellido, fecha de nacimiento y sexo sean correctos.'
        ])->withInput();
    }

    // Crear sesión para el participante (permite re-acceso sin validar sesiones activas)
    Session::put('participant_id', $participant->id);
    Session::put('participant_iuc', $participant->iuc);
    Session::put('institution_id', $institution->id);
    Session::put('participant_access_time', now());

    return redirect()->route('participant.dashboard')
        ->with('success', 'Acceso concedido. Bienvenido de nuevo.');
}

    public function dashboard()
    {
        $participantId = Session::get('participant_id');

        if (!$participantId) {
            return redirect()->route('participant.access.form', ['access_code' => 'default'])
                ->withErrors(['error' => 'Sesión no válida. Por favor, ingresa de nuevo.']);
        }

        $participant = Participant::with(['institution'])->findOrFail($participantId);

        // Cargar sesiones del participante ordenadas por fecha de creación
        $testSessions = TestSession::with(['battery', 'battery.tasks'])
            ->where('participant_id', $participantId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('participant.dashboard', compact('participant', 'testSessions'));
    }

    /**
     * Cerrar sesión del participante
     */
    public function logout()
    {
        Session::forget('participant_id');
        Session::forget('participant_iuc');
        Session::forget('institution_id');
        Session::forget('participant_access_time');

        return redirect()->route('welcome')->with('status', 'Has cerrado sesión correctamente.');
    }
}
