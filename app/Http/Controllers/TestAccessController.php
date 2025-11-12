<?php

namespace App\Http\Controllers;

use App\Models\BatteryCode;
use App\Models\Participant;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TestAccessController extends Controller
{
    /**
     * Mostrar el formulario de acceso con código de batería
     */
    public function showBatteryCodeForm($code)
    {
        // Buscar el código de batería
        $batteryCode = BatteryCode::where('code', $code)->first();

        // Validar que el código existe
        if (!$batteryCode) {
            abort(404, 'Código de batería no encontrado.');
        }

        // Validar que el código es válido
        if (!$batteryCode->isValid()) {
            $reason = '';

            if (!$batteryCode->is_active) {
                $reason = 'Este código ha sido desactivado.';
            } elseif ($batteryCode->isExpired()) {
                $reason = 'Este código ha expirado.';
            } elseif (!$batteryCode->hasUsesAvailable()) {
                $reason = 'Este código ha alcanzado el límite de usos.';
            }

            return view('test.battery-code-invalid', [
                'code' => $code,
                'reason' => $reason,
                'batteryCode' => $batteryCode
            ]);
        }

        // Cargar relaciones
        $batteryCode->load(['battery', 'institution']);

        return view('test.battery-code-access', compact('batteryCode', 'code'));
    }

    /**
     * Validar acceso y crear sesión con código de batería
     */
public function validateBatteryCodeAccess(Request $request, $code)
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

    // Buscar el código de batería
    $batteryCode = BatteryCode::where('code', $code)->first();

    if (!$batteryCode || !$batteryCode->isValid()) {
        return back()->withErrors(['error' => 'Código de batería inválido o expirado.'])->withInput();
    }

    $institution = $batteryCode->institution;

    // Generar el IUC con los datos proporcionados
    $iuc = Participant::generateIUC(
        $institution->access_code,
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
            'error' => 'No se encontró un participante con los datos proporcionados. Verifica que tu nombre, apellido, fecha de nacimiento y sexo sean correctos, o contacta con tu profesor para que te registre.'
        ])->withInput();
    }

    // **CAMBIO IMPORTANTE: Buscar si ya existe una sesión activa**
    $existingSession = TestSession::where('participant_id', $participant->id)
        ->where('battery_id', $batteryCode->battery_id)
        ->whereIn('status', [\App\Enums\SessionStatus::PENDING, \App\Enums\SessionStatus::IN_PROGRESS])
        ->first();

    // Si ya existe una sesión activa, recuperarla
    if ($existingSession) {
        // Crear sesión para el participante
        Session::put('participant_id', $participant->id);
        Session::put('participant_iuc', $participant->iuc);
        Session::put('institution_id', $institution->id);
        Session::put('participant_access_time', now());

        // Redirigir al dashboard con mensaje informativo
        return redirect()->route('participant.dashboard')
            ->with('success', 'Sesión recuperada. Puedes continuar con tu evaluación.');
    }

    // Verificar que la institución tiene usos disponibles SOLO si vamos a crear nueva sesión
    if ($institution->available_uses <= 0) {
        return back()->withErrors([
            'error' => 'La institución ha agotado sus usos disponibles. Contacta con tu profesor.'
        ])->withInput();
    }

    // Verificar que el código tiene usos disponibles
    if (!$batteryCode->hasUsesAvailable()) {
        return back()->withErrors([
            'error' => 'Este código ha alcanzado su límite de usos.'
        ])->withInput();
    }

    // Crear nueva TestSession
    $testSession = TestSession::create([
        'participant_id' => $participant->id,
        'battery_id' => $batteryCode->battery_id,
        'institution_id' => $institution->id,
        'assigned_by_user_id' => null,
        'battery_code_id' => $batteryCode->id,
        'status' => \App\Enums\SessionStatus::PENDING,
        'started_at' => null,
        'completed_at' => null,
        'use_deducted' => false,
    ]);

    // INICIALIZACION DE TAREAS
    $testSession->initializeTasks();

    // Descontar uso del código
    $batteryCode->incrementUses();

    // Descontar uso de la institución
    $testSession->deductUse();

    // Crear sesión para el participante
    Session::put('participant_id', $participant->id);
    Session::put('participant_iuc', $participant->iuc);
    Session::put('institution_id', $institution->id);
    Session::put('participant_access_time', now());

    // Redirigir al dashboard del participante
    return redirect()->route('participant.dashboard')
        ->with('success', 'Sesión creada correctamente. Ya puedes comenzar tu evaluación.');
}
}
