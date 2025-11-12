<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\TestSession;
use App\Enums\SessionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TestSessionController extends Controller
{
    /**
     * Mostrar dashboard de sesión al participante
     */
    public function show(TestSession $testSession)
    {
        // Validar acceso
        $this->validateParticipantAccess($testSession);

        // Cargar batería y tareas
        $testSession->load(['battery', 'testSessionTasks.task']);

        // Calcular progreso
        $totalTasks = $testSession->testSessionTasks->count();
        $completedTasks = $testSession->testSessionTasks
            ->where('status', \App\Enums\TestSessionTaskStatus::COMPLETED)
            ->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        return view('test.session', compact(
            'testSession',
            'totalTasks',
            'completedTasks',
            'progress'
        ));
    }

    /**
     * Iniciar sesión de test
     */
    public function start(TestSession $testSession)
    {
        // Validar acceso
        $this->validateParticipantAccess($testSession);

        // Verificar estado
        if (!$testSession->isPending()) {
            return redirect()->route('test.session.show', $testSession);
        }

        // Iniciar sesión
        $testSession->start();

        // Obtener primera tarea
        $firstTask = $testSession->testSessionTasks()
            ->ordered()
            ->first();

        if (!$firstTask) {
            return redirect()->back()->with('error', 'No hay tareas configuradas');
        }

        // Redirigir usando el router dinámico
        return redirect()->to($firstTask->getExecutionUrl());
    }

    /**
     * Completar sesión completa
     */
    public function complete(TestSession $testSession)
    {
        // Validar acceso
        $this->validateParticipantAccess($testSession);

        // Verificar que todas las tareas están completadas
        $incompleteTasks = $testSession->testSessionTasks()
            ->where('status', '!=', \App\Enums\TestSessionTaskStatus::COMPLETED)
            ->count();

        if ($incompleteTasks > 0) {
            return redirect()->back()->with('error', 'Aún quedan tareas pendientes');
        }

        // Completar sesión
        $testSession->complete();

        // Mostrar vista de finalización
        return view('test.completed', compact('testSession'));
    }

    /**
     * Validar acceso del participante
     */
    private function validateParticipantAccess(TestSession $testSession)
    {
        $participantIuc = Session::get('participant_iuc');

        if (!$participantIuc) {
            abort(403, 'Acceso no autorizado');
        }

        if ($testSession->participant->iuc !== $participantIuc) {
            abort(403, 'No tienes acceso a esta sesión');
        }
    }
}
