<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Institution;
use App\Enums\Sex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Battery;
use App\Models\TestSession;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Query base
        $query = Participant::with(['institution', 'createdBy']);

        // Control de permisos
        if ($user->hasPermissionTo('participants.view-all')) {
            // Administrador: ve TODOS los participantes de TODAS las instituciones
            // No agregamos ningún filtro
        } elseif ($user->hasPermissionTo('participants.view-institution')) {
            // Responsable: ve todos de su institución
            $query->where('institution_id', $user->institution_id);
        } elseif ($user->hasPermissionTo('participants.view-own')) {
            // Profesor: solo ve los suyos
            $query->where('created_by_user_id', $user->id);
        } else {
            // Sin permisos
            abort(403, 'No tienes permisos para ver participantes.');
        }

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('iuc', 'like', "%{$search}%");
        }

        if ($request->filled('sex')) {
            $query->where('sex', $request->sex);
        }

        // Filtro por institución (solo para admins)
        if ($user->hasPermissionTo('participants.view-all') && $request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        $participants = $query->orderBy('created_at', 'desc')->paginate(20);

        // Pasar instituciones si es admin (para filtro)
        $institutions = null;
        if ($user->hasPermissionTo('participants.view-all')) {
            $institutions = Institution::orderBy('name')->get();
        }

        return view('professor.participants.index', compact('participants', 'institutions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sexOptions = Sex::cases();
        return view('professor.participants.create', compact('sexOptions'));
    }

    /**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'birth_date' => 'required|date|before:today',
        'sex' => 'required|in:M,F,O',
    ]);

    $user = Auth::user();
    $institution = $user->institution;

    // Calcular age_months
    $ageMonths = Participant::calculateAgeInMonths($validated['birth_date']);

    // Generar IUG
    $iug = Participant::generateIUG(
        $validated['first_name'],
        $validated['last_name'],
        $validated['birth_date']
    );

    // Generar IUC
    $iuc = Participant::generateIUC(
        $institution->access_code,
        $validated['first_name'],
        $validated['last_name'],
        $validated['birth_date']
    );

    // Crear participante (SIN guardar datos personales)
    $participant = Participant::create([
        'age_months' => $ageMonths,
        'sex' => $validated['sex'],
        'iug' => $iug,
        'iuc' => $iuc,
        'institution_id' => $user->institution_id,
        'created_by_user_id' => $user->id,
    ]);

    return redirect()
        ->route('professor.participants.show', $participant)
        ->with('success', 'Participante creado correctamente. Los datos personales no se han almacenado por protección de datos.');
}

    /**
     * Display the specified resource.
     */
    public function show(Participant $participant)
    {
        $user = Auth::user();

        // Verificar permisos
        if (!$user->hasPermissionTo('participants.view-all') &&
            !$user->hasPermissionTo('participants.view-institution') &&
            $participant->created_by_user_id !== $user->id) {
            abort(403, 'No tienes permisos para ver este participante.');
        }

        // Verificar institución para responsables
        if ($user->hasPermissionTo('participants.view-institution') &&
            !$user->hasPermissionTo('participants.view-all') &&
            $participant->institution_id !== $user->institution_id) {
            abort(403, 'No tienes permisos para ver este participante.');
        }

        $participant->load(['institution', 'createdBy']);

        return view('professor.participants.show', compact('participant'));
    }

            /**
         * Show the form for editing the specified resource.
         */
        public function edit(Participant $participant)
        {
            // No permitir edición porque no tenemos los datos personales originales
            return redirect()
                ->route('professor.participants.show', $participant)
                ->with('error', 'No se pueden editar participantes porque los datos personales no se almacenan por protección de datos.');
        }

        /**
         * Update the specified resource in storage.
         */
        public function update(Request $request, Participant $participant)
        {
            // No permitir edición
            return redirect()
                ->route('professor.participants.show', $participant)
                ->with('error', 'No se pueden editar participantes porque los datos personales no se almacenan por protección de datos.');
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Participant $participant)
    {
        $user = Auth::user();

        // Solo admin, responsables o el creador pueden eliminar
        if (!$user->hasPermissionTo('participants.view-all') &&
            !$user->hasPermissionTo('participants.view-institution') &&
            $participant->created_by_user_id !== $user->id) {
            abort(403, 'No tienes permisos para eliminar este participante.');
        }

        // Verificar institución para responsables
        if ($user->hasPermissionTo('participants.view-institution') &&
            !$user->hasPermissionTo('participants.view-all') &&
            $participant->institution_id !== $user->institution_id) {
            abort(403, 'No tienes permisos para eliminar este participante.');
        }

        $participant->delete();

        return redirect()
            ->route('professor.participants.index')
            ->with('success', 'Participante eliminado correctamente.');
    }

    /**
     * Show form to assign battery to participant
     */
    public function assignBattery(Participant $participant)
    {
        $user = Auth::user();

        // Verificar permisos
        if (!$user->hasPermissionTo('participants.view-all') &&
            !$user->hasPermissionTo('participants.view-institution') &&
            $participant->created_by_user_id !== $user->id) {
            abort(403, 'No tienes permisos para asignar baterías a este participante.');
        }

        // Verificar institución para responsables
        if ($user->hasPermissionTo('participants.view-institution') &&
            !$user->hasPermissionTo('participants.view-all') &&
            $participant->institution_id !== $user->institution_id) {
            abort(403, 'No tienes permisos para asignar baterías a este participante.');
        }

        // Obtener baterías activas
        $batteries = Battery::where('is_active', true)->orderBy('name')->get();

        // Obtener sesiones activas del participante (para mostrar cuáles ya tiene)
        $activeSessions = TestSession::where('participant_id', $participant->id)
            ->whereIn('status', [\App\Enums\SessionStatus::PENDING, \App\Enums\SessionStatus::IN_PROGRESS])
            ->with('battery')
            ->get();

        return view('professor.participants.assign-battery', compact('participant', 'batteries', 'activeSessions'));
    }

    /**
     * Store battery assignment
     */
    public function storeAssignment(Request $request, Participant $participant)
    {
        $user = Auth::user();

        // Verificar permisos
        if (!$user->hasPermissionTo('participants.view-all') &&
            !$user->hasPermissionTo('participants.view-institution') &&
            $participant->created_by_user_id !== $user->id) {
            abort(403, 'No tienes permisos para asignar baterías a este participante.');
        }

        // Verificar institución para responsables
        if ($user->hasPermissionTo('participants.view-institution') &&
            !$user->hasPermissionTo('participants.view-all') &&
            $participant->institution_id !== $user->institution_id) {
            abort(403, 'No tienes permisos para asignar baterías a este participante.');
        }

        $validated = $request->validate([
            'battery_id' => 'required|exists:batteries,id',
        ], [
            'battery_id.required' => 'Debes seleccionar una batería.',
            'battery_id.exists' => 'La batería seleccionada no existe.',
        ]);

        $battery = Battery::findOrFail($validated['battery_id']);

        // Verificar que la batería esté activa
        if (!$battery->is_active) {
            return back()->withErrors(['battery_id' => 'Esta batería no está activa.'])->withInput();
        }

        // Verificar que no tenga sesión activa de esta batería
        $existingSession = TestSession::where('participant_id', $participant->id)
            ->where('battery_id', $battery->id)
            ->whereIn('status', [\App\Enums\SessionStatus::PENDING, \App\Enums\SessionStatus::IN_PROGRESS])
            ->first();

        if ($existingSession) {
            return back()->withErrors([
                'battery_id' => 'El participante ya tiene una sesión pendiente o en progreso de esta batería.'
            ])->withInput();
        }

        $institution = $participant->institution;

        // Verificar que la institución tenga usos disponibles
        if ($institution->available_uses <= 0) {
            return back()->withErrors([
                'error' => 'La institución no tiene usos disponibles. Contacta con el administrador.'
            ])->withInput();
        }

        // Crear TestSession
        $testSession = TestSession::create([
            'participant_id' => $participant->id,
            'battery_id' => $battery->id,
            'institution_id' => $institution->id,
            'assigned_by_user_id' => $user->id, // Asignada por el profesor
            'battery_code_id' => null, // No es por código
            'status' => \App\Enums\SessionStatus::PENDING,
            'started_at' => null,
            'completed_at' => null,
            'use_deducted' => false,
        ]);

        // INICIALIZACION DE TAREAS
        $testSession->initializeTasks();

        // Descontar uso de la institución
        $testSession->deductUse();

        return redirect()
            ->route('professor.participants.show', $participant)
            ->with('success', "Batería '{$battery->name}' asignada correctamente al participante.");
    }
}
