<?php

namespace App\Tests\Matrix\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\TestSession;
use App\Models\TestSessionTask;
use App\Tests\Matrix\Models\MatrixResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MatrixTestController extends Controller
{
/**
 * Mostrar la tarea con sus instrucciones
 * GET /test/matrix/session/{testSession}/task/{testSessionTask}
 */
public function showTask(TestSession $testSession, TestSessionTask $testSessionTask)
{
    // Validar acceso del participante
    $this->validateParticipantAccess($testSession);

    // Validar que la tarea pertenece a la sesión
    if ($testSessionTask->test_session_id !== $testSession->id) {
        abort(404, 'Tarea no encontrada');
    }

    // Validar que es tipo MATRIX
    if (!$testSessionTask->task->isMatrix()) {
        abort(400, 'Tipo de tarea incorrecto');
    }

    // ⭐ AGREGAR ESTA LÓGICA AQUÍ ⭐
    // Si la tarea ya está IN_PROGRESS, redirigir al siguiente ítem pendiente
    if ($testSessionTask->isInProgress()) {
        $nextItem = $this->getNextItem($testSessionTask);

        if ($nextItem) {
            // Hay un ítem pendiente, redirigir directamente
            return redirect()->route('test.matrix.item.show', [
                'testSessionTask' => $testSessionTask->id,
                'item' => $nextItem->id
            ]);
        }

        // Si no hay más ítems, verificar si debe completarse
        $totalItems = $testSessionTask->task->activeItems()->count();
        $answeredItems = $testSessionTask->matrixResponses()->count();

        if ($answeredItems >= $totalItems) {
            // Completar tarea automáticamente
            $testSessionTask->complete();

            // Buscar siguiente tarea
            $nextTask = TestSessionTask::where('test_session_id', $testSessionTask->test_session_id)
                ->where('status', \App\Enums\TestSessionTaskStatus::NOT_STARTED)
                ->ordered()
                ->first();

            if ($nextTask) {
                return redirect()->to($nextTask->getExecutionUrl());
            } else {
                $testSession->complete();
                return view('test.completed', compact('testSession'));
            }
        }
    }
    // ⭐ FIN DE LA LÓGICA AGREGADA ⭐

    // Cargar items activos ordenados por dificultad
    $items = $testSessionTask->task->activeItems()
        ->orderedByDifficulty()
        ->get();

    // Calcular progreso
    $totalItems = $items->count();
    $answeredItems = $testSessionTask->matrixResponses()->count();
    $progress = $totalItems > 0 ? round(($answeredItems / $totalItems) * 100) : 0;

    // Resto del código original para cuando la tarea está NOT_STARTED...
    return view('tests.matrix.task', compact(
        'testSession',
        'testSessionTask',
        'items',
        'totalItems',
        'answeredItems',
        'progress'
    ));
}

    /**
     * Iniciar una tarea
     * POST /test/matrix/task/{testSessionTask}/start
     */
    public function startTask(TestSessionTask $testSessionTask)
    {
        // Validar estado
        if (!$testSessionTask->isNotStarted()) {
            $firstItem = $this->getNextItem($testSessionTask);
            if ($firstItem) {
                return redirect()->route('test.matrix.item.show', [
                    'testSessionTask' => $testSessionTask->id,
                    'item' => $firstItem->id
                ]);
            }
        }

        // Iniciar tarea
        $testSessionTask->start();

        // Redirigir a primer item
        $firstItem = $this->getNextItem($testSessionTask);

        return redirect()->route('test.matrix.item.show', [
            'testSessionTask' => $testSessionTask->id,
            'item' => $firstItem->id
        ]);
    }

    /**
     * Mostrar un item específico
     * GET /test/matrix/task/{testSessionTask}/item/{item}
     */
    public function showItem(TestSessionTask $testSessionTask, Item $item)
    {
        // Validar que el item pertenece a la tarea
        if ($item->task_id !== $testSessionTask->task_id) {
            abort(404, 'Item no encontrado');
        }

        // Validar que el item está activo
        if (!$item->is_active) {
            abort(404, 'Item no disponible');
        }

        // Verificar si ya existe respuesta
        $existingResponse = MatrixResponse::where('test_session_task_id', $testSessionTask->id)
            ->where('item_id', $item->id)
            ->first();

        if ($existingResponse) {
            // Si ya respondió, ir al siguiente
            $nextItem = $this->getNextItem($testSessionTask);

            if ($nextItem) {
                return redirect()->route('test.matrix.item.show', [
                    'testSessionTask' => $testSessionTask->id,
                    'item' => $nextItem->id
                ]);
            } else {
                // No hay más items, completar tarea
                return redirect()->route('test.matrix.task.complete', [
                    'testSessionTask' => $testSessionTask->id
                ]);
            }
        }

        // Calcular progreso
        $totalItems = $testSessionTask->task->activeItems()->count();
        $currentItemNumber = $testSessionTask->matrixResponses()->count() + 1;

        return view('tests.matrix.item', compact(
            'testSessionTask',
            'item',
            'totalItems',
            'currentItemNumber'
        ));
    }

    /**
     * Registrar respuesta del participante (AJAX)
     * POST /test/matrix/task/{testSessionTask}/item/{item}/response
     */
    public function submitResponse(Request $request, TestSessionTask $testSessionTask, Item $item)
    {
        // Validar datos
        $validated = $request->validate([
            'answer' => 'required|string',
            'response_time_ms' => 'required|integer|min:0'
        ]);

        // Verificar que no existe respuesta previa
        $existingResponse = MatrixResponse::where('test_session_task_id', $testSessionTask->id)
            ->where('item_id', $item->id)
            ->first();

        if ($existingResponse) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una respuesta para este ítem'
            ], 400);
        }

        // Calcular corrección
        $isCorrect = ($validated['answer'] === $item->correct_answer);

        // Guardar respuesta
        MatrixResponse::create([
            'test_session_task_id' => $testSessionTask->id,
            'item_id' => $item->id,
            'participant_answer' => $validated['answer'],
            'is_correct' => $isCorrect,
            'response_time_ms' => $validated['response_time_ms'],
        ]);

        // Determinar siguiente acción
        return $this->determineNextAction($testSessionTask);
    }

    /**
     * Completar tarea
     * POST /test/matrix/task/{testSessionTask}/complete
     */
    public function completeTask(TestSessionTask $testSessionTask)
    {
        // Verificar que todos los items tienen respuesta
        $totalItems = $testSessionTask->task->activeItems()->count();
        $totalResponses = $testSessionTask->matrixResponses()->count();

        if ($totalResponses < $totalItems) {
            return redirect()->back()->with('error', 'Aún quedan ítems por responder');
        }

        // Completar tarea
        $testSessionTask->complete();

        // Buscar siguiente tarea
        $nextTask = TestSessionTask::where('test_session_id', $testSessionTask->test_session_id)
            ->where('status', \App\Enums\TestSessionTaskStatus::NOT_STARTED)
            ->ordered()
            ->first();

        if ($nextTask) {
            // Redirigir a siguiente tarea (usando router dinámico)
            return redirect()->to($nextTask->getExecutionUrl());
        } else {
            // No hay más tareas, completar sesión
            return redirect()->route('test.session.complete', [
                'testSession' => $testSessionTask->test_session_id
            ]);
        }
    }

    /**
     * MÉTODOS PRIVADOS
     */

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

        if (!$testSession->isInProgress()) {
            abort(400, 'La sesión no está en progreso');
        }
    }

    /**
     * Obtener siguiente item sin responder
     */
    private function getNextItem(TestSessionTask $testSessionTask): ?Item
    {
        // IDs de items ya respondidos
        $answeredItemIds = $testSessionTask->matrixResponses()
            ->pluck('item_id')
            ->toArray();

        // Buscar primer item sin responder
        return $testSessionTask->task->activeItems()
            ->orderedByDifficulty()
            ->whereNotIn('id', $answeredItemIds)
            ->first();
    }

    /**
     * Determinar siguiente acción después de responder
     */
    private function determineNextAction(TestSessionTask $testSessionTask)
    {
        // Contar respuestas y items totales
        $totalItems = $testSessionTask->task->activeItems()->count();
        $totalResponses = $testSessionTask->matrixResponses()->count();

        if ($totalResponses >= $totalItems) {
            // Tarea completada
            $testSessionTask->complete();

            // Buscar siguiente tarea
            $nextTask = TestSessionTask::where('test_session_id', $testSessionTask->test_session_id)
                ->where('status', \App\Enums\TestSessionTaskStatus::NOT_STARTED)
                ->ordered()
                ->first();

            if ($nextTask) {
                return response()->json([
                    'success' => true,
                    'task_completed' => true,
                    'next_task_url' => $nextTask->getExecutionUrl()
                ]);
            } else {
                // Todas las tareas completadas
                $testSession = TestSession::find($testSessionTask->test_session_id);
                $testSession->complete();

                return response()->json([
                    'success' => true,
                    'session_completed' => true,
                    'completion_url' => route('test.session.show', [
                        'testSession' => $testSessionTask->test_session_id
                    ])
                ]);
            }
        } else {
            // Hay más items en esta tarea
            $nextItem = $this->getNextItem($testSessionTask);

            return response()->json([
                'success' => true,
                'next_item' => true,
                'next_item_url' => route('test.matrix.item.show', [
                    'testSessionTask' => $testSessionTask->id,
                    'item' => $nextItem->id
                ])
            ]);
        }
    }
}
