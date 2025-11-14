<?php

namespace App\Tests\Spatial\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TestSessionTask;
use App\Models\Item;
use App\Tests\Spatial\Models\SpatialResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpatialTestController extends Controller
{
    /**
     * Mostrar instrucciones de la tarea spatial
     */
    public function showTask(TestSessionTask $testSessionTask)
    {
        // Verificar que la tarea es de tipo spatial
        if (!$testSessionTask->task->isSpatial()) {
            abort(404, 'Esta tarea no es de tipo visoespacial.');
        }

        // Verificar permisos de acceso
        $this->authorizeParticipantAccess($testSessionTask);

        // Obtener items de la tarea
        $items = $testSessionTask->task->activeItems()->orderedByDifficulty()->get();
        $totalItems = $items->count();

        // Verificar si ya hay items respondidos
        $answeredCount = SpatialResponse::where('test_session_task_id', $testSessionTask->id)->count();

        return view('tests.spatial.task', compact('testSessionTask', 'items', 'totalItems', 'answeredCount'));
    }

    /**
     * Mostrar un item específico
     */
    public function showItem(TestSessionTask $testSessionTask, Item $item)
    {
        // Verificar que la tarea es de tipo spatial
        if (!$testSessionTask->task->isSpatial()) {
            abort(404, 'Esta tarea no es de tipo visoespacial.');
        }

        // Verificar que el item pertenece a esta tarea
        if ($item->task_id !== $testSessionTask->task_id) {
            abort(404, 'Este ítem no pertenece a esta tarea.');
        }

        // Verificar permisos de acceso
        $this->authorizeParticipantAccess($testSessionTask);

        // Verificar que el item no ha sido respondido ya
        $alreadyAnswered = SpatialResponse::where('test_session_task_id', $testSessionTask->id)
            ->where('item_id', $item->id)
            ->exists();

        if ($alreadyAnswered) {
            // Si ya fue respondido, redirigir al siguiente item o a completar tarea
            return $this->redirectToNextItem($testSessionTask, $item);
        }

        // Calcular progreso
        $allItems = $testSessionTask->task->activeItems()->orderedByDifficulty()->get();
        $totalItems = $allItems->count();
        $currentItemNumber = $allItems->search(function($i) use ($item) {
            return $i->id === $item->id;
        }) + 1;

        return view('tests.spatial.item', compact('testSessionTask', 'item', 'totalItems', 'currentItemNumber'));
    }

    /**
     * Guardar respuesta de un item
     */
    public function submitResponse(Request $request, TestSessionTask $testSessionTask, Item $item)
    {
        // Validar entrada
        $validated = $request->validate([
            'answer' => 'required|string|in:1,2,3,4,5,6',
            'response_time_ms' => 'required|integer|min:0'
        ]);

        // Verificar que la tarea es de tipo spatial
        if (!$testSessionTask->task->isSpatial()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta tarea no es de tipo visoespacial.'
            ], 400);
        }

        // Verificar que el item pertenece a esta tarea
        if ($item->task_id !== $testSessionTask->task_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este ítem no pertenece a esta tarea.'
            ], 400);
        }

        // Verificar permisos de acceso
        $this->authorizeParticipantAccess($testSessionTask);

        // Verificar que no se ha respondido ya
        $alreadyAnswered = SpatialResponse::where('test_session_task_id', $testSessionTask->id)
            ->where('item_id', $item->id)
            ->exists();

        if ($alreadyAnswered) {
            return response()->json([
                'success' => false,
                'message' => 'Este ítem ya fue respondido.'
            ], 400);
        }

        try {
            DB::beginTransaction();


            // Verificar si es correcta
            $isCorrect = ($validated['answer'] === $item->correct_answer);

            // Guardar respuesta
            SpatialResponse::create([
                'test_session_task_id' => $testSessionTask->id,
                'item_id' => $item->id,
                'participant_answer' => $validated['answer'],
                'is_correct' => $isCorrect,
                'response_time_ms' => $validated['response_time_ms']
            ]);

            // Actualizar estado de la tarea si es necesario
            $this->updateTaskStatus($testSessionTask);

            DB::commit();

            // Determinar siguiente acción
            return $this->determineNextAction($testSessionTask, $item);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la respuesta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * MÉTODOS PRIVADOS
     */

    private function authorizeParticipantAccess(TestSessionTask $testSessionTask)
    {
        // Verificar que el participante en sesión tiene acceso a esta tarea
        $participantIuc = session('participant_iuc');

        if (!$participantIuc) {
            abort(403, 'Acceso no autorizado.');
        }

        if ($testSessionTask->testSession->participant->iuc !== $participantIuc) {
            abort(403, 'No tienes permiso para acceder a esta evaluación.');
        }
    }

    private function updateTaskStatus(TestSessionTask $testSessionTask)
    {
        // Contar respuestas
        $answeredCount = SpatialResponse::where('test_session_task_id', $testSessionTask->id)->count();
        $totalItems = $testSessionTask->task->activeItems()->count();

        // Si se respondieron todos los items, marcar como completada
        if ($answeredCount >= $totalItems) {
            $testSessionTask->markAsCompleted();
        } elseif ($testSessionTask->status === \App\Enums\TestSessionTaskStatus::NOT_STARTED) {
            // Si es la primera respuesta, marcar como en progreso
            $testSessionTask->markAsInProgress();
        }
    }

    private function determineNextAction(TestSessionTask $testSessionTask, Item $currentItem)
    {
        // Obtener todos los items de la tarea
        $allItems = $testSessionTask->task->activeItems()->orderedByDifficulty()->get();

        // Buscar índice del item actual
        $currentIndex = $allItems->search(function($item) use ($currentItem) {
            return $item->id === $currentItem->id;
        });

        // Si hay siguiente item en esta tarea
        if ($currentIndex !== false && $currentIndex < $allItems->count() - 1) {
            $nextItem = $allItems[$currentIndex + 1];

            return response()->json([
                'success' => true,
                'next_item' => true,
                'next_item_url' => route('test.spatial.item', [
                    'testSessionTask' => $testSessionTask->id,
                    'item' => $nextItem->id
                ])
            ]);
        }

        // Tarea completada, verificar si hay más tareas
        $testSession = $testSessionTask->testSession;
        $nextTask = $testSession->getNextPendingTask();

        if ($nextTask) {
            return response()->json([
                'success' => true,
                'task_completed' => true,
                'next_task_url' => $nextTask->getExecutionUrl()
            ]);
        }

        // Batería completada
        $testSession->markAsCompleted();

        return response()->json([
            'success' => true,
            'session_completed' => true,
            'completion_url' => route('test.session.complete', $testSession)
        ]);
    }

    private function redirectToNextItem(TestSessionTask $testSessionTask, Item $currentItem)
    {
        // Obtener todos los items
        $allItems = $testSessionTask->task->activeItems()->orderedByDifficulty()->get();

        // Buscar índice del item actual
        $currentIndex = $allItems->search(function($item) use ($currentItem) {
            return $item->id === $currentItem->id;
        });

        // Si hay siguiente item
        if ($currentIndex !== false && $currentIndex < $allItems->count() - 1) {
            $nextItem = $allItems[$currentIndex + 1];
            return redirect()->route('test.spatial.item', [
                'testSessionTask' => $testSessionTask->id,
                'item' => $nextItem->id
            ]);
        }

        // No hay más items, ir a sesión
        return redirect()->route('test.session.show', $testSessionTask->testSession);
    }
}
