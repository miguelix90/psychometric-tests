<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Item;
use App\Models\Battery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Enums\TaskType;

class DemoController extends Controller
{
    /**
     * Iniciar demo de una tarea específica
     */
    public function start(Task $task)
    {
        // Verificar que es administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'Solo los administradores pueden acceder al modo demo.');
        }

        // Verificar que la tarea tiene items
        if ($task->activeItems()->count() === 0) {
            return redirect()
                ->back()
                ->with('error', 'Esta tarea no tiene ítems activos.');
        }

        // Limpiar cualquier sesión demo anterior
        $this->clearDemoSession();

        // Crear sesión demo
        Session::put('demo_mode', true);
        Session::put('demo_task_id', $task->id);
        Session::put('demo_responses', []);
        Session::put('demo_started_at', now());

        // Redirigir directamente a las instrucciones de la tarea
        return redirect()->route('admin.demo.task.show');
    }

    /**
     * Mostrar instrucciones de la tarea en demo
     */
    public function showTask()
    {
        if (!Session::has('demo_mode')) {
            abort(403, 'Sesión demo no válida.');
        }

        $taskId = Session::get('demo_task_id');
        $task = Task::findOrFail($taskId);

        // Obtener items de la tarea
        $items = $task->activeItems()->orderedByDifficulty()->get();
        $totalItems = $items->count();

        return view('admin.demo.task', compact('task', 'items', 'totalItems'));
    }

    /**
     * Iniciar tarea en modo demo
     */
    public function startTask()
    {
        if (!Session::has('demo_mode')) {
            abort(403, 'Sesión demo no válida.');
        }

        $taskId = Session::get('demo_task_id');
        $task = Task::findOrFail($taskId);

        // Obtener primer item
        $firstItem = $task->activeItems()->orderedByDifficulty()->first();

        if (!$firstItem) {
            return redirect()->route('admin.demo.task.show')
                ->with('error', 'Esta tarea no tiene ítems activos.');
        }

        // Redirigir según tipo de tarea
        if ($task->isMatrix()) {
            return redirect()->route('admin.demo.matrix.item', [
                'itemId' => $firstItem->id
            ]);
        }

        if ($task->isSpatial()) {
            return redirect()->route('admin.demo.spatial.item', [
                'itemId' => $firstItem->id
            ]);
        }

        // Agregar otros tipos cuando se implementen
        return redirect()->route('admin.demo.task.show')
            ->with('error', 'Tipo de tarea no soportado en demo aún.');
    }

    /**
     * Mostrar item Matrix en modo demo (REUTILIZA VISTA EXISTENTE)
     */
    public function showMatrixItem($itemId)
    {
        if (!Session::has('demo_mode')) {
            abort(403, 'Sesión demo no válida.');
        }

        $taskId = Session::get('demo_task_id');
        $task = Task::findOrFail($taskId);
        $item = Item::where('task_id', $taskId)->where('id', $itemId)->firstOrFail();

        // Calcular progreso
        $allItems = $task->activeItems()->orderedByDifficulty()->get();
        $totalItems = $allItems->count();
        $currentItemNumber = $allItems->search(function($i) use ($item) {
            return $i->id === $item->id;
        }) + 1;

        // Crear objeto simulado de TestSessionTask para compatibilidad
        $testSessionTask = (object)[
            'id' => 'demo',
            'task' => $task,
            'task_id' => $task->id
        ];

        // REUTILIZAR LA VISTA EXISTENTE con indicador de modo demo
        return view('tests.matrix.item', compact(
            'testSessionTask',
            'item',
            'totalItems',
            'currentItemNumber'
        ))->with('demoMode', true);
    }

    /**
     * Mostrar item Spatial en modo demo (REUTILIZA VISTA EXISTENTE)
     */
    public function showSpatialItem($itemId)
    {
        if (!Session::has('demo_mode')) {
            abort(403, 'Sesión demo no válida.');
        }

        $taskId = Session::get('demo_task_id');
        $task = Task::findOrFail($taskId);
        $item = Item::where('task_id', $taskId)->where('id', $itemId)->firstOrFail();

        // Calcular progreso
        $allItems = $task->activeItems()->orderedByDifficulty()->get();
        $totalItems = $allItems->count();
        $currentItemNumber = $allItems->search(function($i) use ($item) {
            return $i->id === $item->id;
        }) + 1;

        // Crear objeto simulado de TestSessionTask para compatibilidad
        $testSessionTask = (object)[
            'id' => 'demo',
            'task' => $task,
            'task_id' => $task->id
        ];

        // REUTILIZAR LA VISTA EXISTENTE con indicador de modo demo
        return view('tests.spatial.item', compact(
            'testSessionTask',
            'item',
            'totalItems',
            'currentItemNumber'
        ))->with('demoMode', true);
    }

    /**
     * Guardar respuesta demo (no se guarda en BD)
     */
    public function submitMatrixResponse(Request $request, $itemId)
    {
        if (!Session::has('demo_mode')) {
            return response()->json(['success' => false, 'message' => 'Sesión demo no válida'], 403);
        }

        $validated = $request->validate([
            'answer' => 'required|string',
            'response_time_ms' => 'required|integer|min:0'
        ]);

        $taskId = Session::get('demo_task_id');
        $task = Task::findOrFail($taskId);
        $item = Item::findOrFail($itemId);

        // Guardar respuesta en sesión (temporal, no en BD)
        $responses = Session::get('demo_responses', []);
        $responses[] = [
            'task_id' => $taskId,
            'item_id' => $itemId,
            'answer' => $validated['answer'],
            'is_correct' => ($validated['answer'] === $item->correct_answer),
            'response_time_ms' => $validated['response_time_ms'],
            'timestamp' => now()->toDateTimeString()
        ];
        Session::put('demo_responses', $responses);

        // Determinar siguiente item
        return $this->determineNextAction($task, $item);
    }

/**
 * Guardar respuesta demo Spatial (no se guarda en BD)
 */
public function submitSpatialResponse(Request $request, $itemId)
{
    if (!Session::has('demo_mode')) {
        return response()->json(['success' => false, 'message' => 'Sesión demo no válida'], 403);
    }

    // CAMBIAR ESTA VALIDACIÓN
    $validated = $request->validate([
        'answer' => 'required|string|in:1,2,3,4,5,6',
        'response_time_ms' => 'required|integer|min:0'
    ]);

    $taskId = Session::get('demo_task_id');
    $task = Task::findOrFail($taskId);
    $item = Item::findOrFail($itemId);

    // Guardar respuesta en sesión (temporal, no en BD)
    $responses = Session::get('demo_responses', []);
    $responses[] = [
        'task_id' => $taskId,
        'item_id' => $itemId,
        'answer' => $validated['answer'],
        'is_correct' => ($validated['answer'] === $item->correct_answer),
        'response_time_ms' => $validated['response_time_ms'],
        'timestamp' => now()->toDateTimeString()
    ];
    Session::put('demo_responses', $responses);

    // Determinar siguiente item
    return $this->determineNextAction($task, $item);
}

    /**
 * Resetear demo
 */
public function reset()
{
    $taskId = Session::get('demo_task_id');

    if (!$taskId) {
        return redirect()->route('admin.tasks.index');
    }

    // Limpiar y reiniciar
    $this->clearDemoSession();

    return redirect()->route('admin.demo.start', ['task' => $taskId])
        ->with('success', 'Demo reseteado correctamente.');
}

    /**
     * Salir del modo demo
     */
    public function exit()
    {
        $taskId = Session::get('demo_task_id');
        $this->clearDemoSession();

        if ($taskId) {
            $task = Task::find($taskId);
            if ($task) {
                return redirect()->route('admin.tasks.show', $task)
                    ->with('success', 'Has salido del modo demo.');
            }
        }

        return redirect()->route('admin.tasks.index');
    }

    /**
     * Vista de demo completado
     */
    public function completed()
    {
        if (!Session::has('demo_mode')) {
            return redirect()->route('admin.items.index');
        }

        $taskId = Session::get('demo_task_id');
        $task = Task::findOrFail($taskId);
        $responses = Session::get('demo_responses', []);

        return view('admin.demo.completed', compact('task', 'responses'));
    }

    /**
     * ========================================
     * DEMO DE ITEM INDIVIDUAL (NIVEL 1)
     * ========================================
     */

    /**
     * Iniciar demo de un ítem individual
     */
    public function startItem(Item $item)
    {
        // Verificar que es administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'Solo los administradores pueden acceder al modo demo.');
        }

        // Verificar que el ítem está activo
        if (!$item->is_active) {
            return redirect()
                ->back()
                ->with('error', 'Este ítem no está activo.');
        }

        // Limpiar cualquier sesión demo anterior
        $this->clearDemoSession();

        // Crear sesión demo de ITEM
        Session::put('demo_mode', true);
        Session::put('demo_type', 'item');
        Session::put('demo_item_id', $item->id);
        Session::put('demo_started_at', now());

        // Redirigir según tipo de tarea
        if ($item->task->isMatrix()) {
            return redirect()->route('admin.demo.item.matrix', ['item' => $item->id]);
        }

        // Agregar otros tipos cuando se implementen
        return redirect()
            ->back()
            ->with('error', 'Tipo de tarea no soportado en demo aún.');
    }

    /**
     * Mostrar item Matrix individual en modo demo
     */
    public function showItemMatrix($itemId)
    {
        if (!Session::has('demo_mode') || Session::get('demo_type') !== 'item') {
            abort(403, 'Sesión demo no válida.');
        }

        $item = Item::findOrFail($itemId);
        $task = $item->task;

        // Para item individual: siempre es 1 de 1
        $totalItems = 1;
        $currentItemNumber = 1;

        // Crear objeto simulado de TestSessionTask para compatibilidad
        $testSessionTask = (object)[
            'id' => 'demo-item',
            'task' => $task,
            'task_id' => $task->id
        ];

        // REUTILIZAR LA VISTA EXISTENTE con indicador de modo demo
        return view('tests.matrix.item', compact(
            'testSessionTask',
            'item',
            'totalItems',
            'currentItemNumber'
        ))->with('demoMode', true)->with('demoType', 'item');
    }

    /**
     * Guardar respuesta demo de item individual (no se guarda en BD)
     */
    public function submitItemResponse(Request $request, $itemId)
    {
        if (!Session::has('demo_mode') || Session::get('demo_type') !== 'item') {
            return response()->json(['success' => false, 'message' => 'Sesión demo no válida'], 403);
        }

        $validated = $request->validate([
            'answer' => 'required|string',
            'response_time_ms' => 'required|integer|min:0'
        ]);

        $item = Item::findOrFail($itemId);

        // Guardar respuesta en sesión (temporal, no en BD)
        $response = [
            'item_id' => $itemId,
            'answer' => $validated['answer'],
            'is_correct' => ($validated['answer'] === $item->correct_answer),
            'response_time_ms' => $validated['response_time_ms'],
            'timestamp' => now()->toDateTimeString()
        ];
        Session::put('demo_item_response', $response);

        // Para demo de item: siempre vuelve a la vista del item
        return response()->json([
            'success' => true,
            'demo_item_completed' => true,
            'completion_url' => route('admin.items.show', $item->id)
        ]);
    }

    /**
     * ========================================
     * DEMO DE BATERÍA COMPLETA (NIVEL 3)
     * ========================================
     */

    /**
     * Iniciar demo de una batería completa
     */
    public function startBattery(Battery $battery)
    {
        // Verificar que es administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'Solo los administradores pueden acceder al modo demo.');
        }

        // Verificar que la batería tiene tareas
        if ($battery->tasks()->count() === 0) {
            return redirect()
                ->back()
                ->with('error', 'Esta batería no tiene tareas asignadas.');
        }

        // Verificar que al menos una tarea tiene items activos
        $hasActiveItems = false;
        foreach ($battery->tasks as $task) {
            if ($task->activeItems()->count() > 0) {
                $hasActiveItems = true;
                break;
            }
        }

        if (!$hasActiveItems) {
            return redirect()
                ->back()
                ->with('error', 'Esta batería no tiene tareas con ítems activos.');
        }

        // Limpiar cualquier sesión demo anterior
        $this->clearDemoSession();

        // Crear sesión demo de BATERÍA
        Session::put('demo_mode', true);
        Session::put('demo_type', 'battery');
        Session::put('demo_battery_id', $battery->id);
        Session::put('demo_responses', []);
        Session::put('demo_current_task_index', 0);
        Session::put('demo_started_at', now());

        // Redirigir a las instrucciones de la batería
        return redirect()->route('admin.demo.battery.show');
    }

    /**
     * Mostrar instrucciones de la batería en demo
     */
    public function showBattery()
    {
        if (!Session::has('demo_mode') || Session::get('demo_type') !== 'battery') {
            abort(403, 'Sesión demo no válida.');
        }

        $batteryId = Session::get('demo_battery_id');
        $battery = Battery::with(['tasks' => function($query) {
            $query->orderBy('battery_tasks.order');
        }])->findOrFail($batteryId);

        // Contar items totales
        $totalItems = 0;
        foreach ($battery->tasks as $task) {
            $totalItems += $task->activeItems()->count();
        }

        return view('admin.demo.battery', compact('battery', 'totalItems'));
    }

    /**
     * Iniciar batería en modo demo
     */
    public function startBatteryExecution()
    {
        if (!Session::has('demo_mode') || Session::get('demo_type') !== 'battery') {
            abort(403, 'Sesión demo no válida.');
        }

        $batteryId = Session::get('demo_battery_id');
        $battery = Battery::with(['tasks' => function($query) {
            $query->orderBy('battery_tasks.order');
        }])->findOrFail($batteryId);

        // Obtener primera tarea con items
        $firstTask = null;
        foreach ($battery->tasks as $task) {
            if ($task->activeItems()->count() > 0) {
                $firstTask = $task;
                break;
            }
        }

        if (!$firstTask) {
            return redirect()->route('admin.demo.battery.show')
                ->with('error', 'No hay tareas con ítems activos en esta batería.');
        }

        // Guardar la tarea actual
        Session::put('demo_current_task_id', $firstTask->id);

        // Redirigir a las instrucciones de la primera tarea
        return redirect()->route('admin.demo.battery.task.show');
    }

    /**
     * Mostrar instrucciones de tarea actual en demo de batería
     */
    public function showBatteryTask()
    {
        if (!Session::has('demo_mode') || Session::get('demo_type') !== 'battery') {
            abort(403, 'Sesión demo no válida.');
        }

        $taskId = Session::get('demo_current_task_id');
        $task = Task::findOrFail($taskId);

        $batteryId = Session::get('demo_battery_id');
        $battery = Battery::with(['tasks' => function($query) {
            $query->orderBy('battery_tasks.order');
        }])->findOrFail($batteryId);

        // Calcular progreso
        $tasks = $battery->tasks;
        $currentTaskNumber = $tasks->search(function($t) use ($task) {
            return $t->id === $task->id;
        }) + 1;
        $totalTasks = $tasks->count();

        // Obtener items de la tarea
        $items = $task->activeItems()->orderedByDifficulty()->get();
        $totalItems = $items->count();

        return view('admin.demo.battery-task', compact('task', 'battery', 'items', 'totalItems', 'currentTaskNumber', 'totalTasks'));
    }

    /**
     * Iniciar tarea actual en modo demo de batería
     */
    public function startBatteryTask()
    {
        if (!Session::has('demo_mode') || Session::get('demo_type') !== 'battery') {
            abort(403, 'Sesión demo no válida.');
        }

        $taskId = Session::get('demo_current_task_id');
        $task = Task::findOrFail($taskId);

        // Obtener primer item
        $firstItem = $task->activeItems()->orderedByDifficulty()->first();

        if (!$firstItem) {
            return redirect()->route('admin.demo.battery.task.show')
                ->with('error', 'Esta tarea no tiene ítems activos.');
        }

        // Redirigir según tipo de tarea
        if ($task->isMatrix()) {
            return redirect()->route('admin.demo.battery.matrix.item', [
                'itemId' => $firstItem->id
            ]);
        }

        if ($task->isSpatial()) {
            return redirect()->route('admin.demo.battery.spatial.item', [
            'itemId' => $firstItem->id
            ]);
}

        // Agregar otros tipos cuando se implementen
        return redirect()->route('admin.demo.battery.task.show')
            ->with('error', 'Tipo de tarea no soportado en demo aún.');
    }

    /**
     * Mostrar item Matrix en modo demo de batería
     */
    public function showBatteryMatrixItem($itemId)
    {
        if (!Session::has('demo_mode') || Session::get('demo_type') !== 'battery') {
            abort(403, 'Sesión demo no válida.');
        }

        $taskId = Session::get('demo_current_task_id');
        $task = Task::findOrFail($taskId);
        $item = Item::where('task_id', $taskId)->where('id', $itemId)->firstOrFail();

        // Calcular progreso
        $allItems = $task->activeItems()->orderedByDifficulty()->get();
        $totalItems = $allItems->count();
        $currentItemNumber = $allItems->search(function($i) use ($item) {
            return $i->id === $item->id;
        }) + 1;

        // Crear objeto simulado de TestSessionTask para compatibilidad
        $testSessionTask = (object)[
            'id' => 'demo-battery',
            'task' => $task,
            'task_id' => $task->id
        ];

        // REUTILIZAR LA VISTA EXISTENTE con indicador de modo demo
        return view('tests.matrix.item', compact(
            'testSessionTask',
            'item',
            'totalItems',
            'currentItemNumber'
        ))->with('demoMode', true)->with('demoType', 'battery');
    }

    /**
     * Mostrar item Spatial en modo demo de batería
     */
    public function showBatterySpatialItem($itemId)
    {
        if (!Session::has('demo_mode') || Session::get('demo_type') !== 'battery') {
            abort(403, 'Sesión demo no válida.');
        }

        $taskId = Session::get('demo_current_task_id');
        $task = Task::findOrFail($taskId);
        $item = Item::where('task_id', $taskId)->where('id', $itemId)->firstOrFail();

        // Calcular progreso
        $allItems = $task->activeItems()->orderedByDifficulty()->get();
        $totalItems = $allItems->count();
        $currentItemNumber = $allItems->search(function($i) use ($item) {
            return $i->id === $item->id;
        }) + 1;

        // Crear objeto simulado de TestSessionTask para compatibilidad
        $testSessionTask = (object)[
            'id' => 'demo-battery',
            'task' => $task,
            'task_id' => $task->id
        ];

        // REUTILIZAR LA VISTA EXISTENTE con indicador de modo demo
        return view('tests.spatial.item', compact(
            'testSessionTask',
            'item',
            'totalItems',
            'currentItemNumber'
        ))->with('demoMode', true)->with('demoType', 'battery');
    }

/**
 * Guardar respuesta demo de batería (no se guarda en BD)
 */
public function submitBatteryResponse(Request $request, $itemId)
{
    if (!Session::has('demo_mode') || Session::get('demo_type') !== 'battery') {
        return response()->json(['success' => false, 'message' => 'Sesión demo no válida'], 403);
    }

    // CAMBIAR ESTA VALIDACIÓN
    $validated = $request->validate([
        'answer' => 'required|string|regex:/^[1-8]$/',  // Acepta números 1-8
        'response_time_ms' => 'required|integer|min:0'
    ]);

    $taskId = Session::get('demo_current_task_id');
    $task = Task::findOrFail($taskId);
    $item = Item::findOrFail($itemId);

    // Guardar respuesta en sesión (temporal, no en BD)
    $responses = Session::get('demo_responses', []);
    $responses[] = [
        'task_id' => $taskId,
        'item_id' => $itemId,
        'answer' => $validated['answer'],
        'is_correct' => ($validated['answer'] === $item->correct_answer),
        'response_time_ms' => $validated['response_time_ms'],
        'timestamp' => now()->toDateTimeString()
    ];
    Session::put('demo_responses', $responses);

    // Determinar siguiente acción
    return $this->determineNextActionBattery($task, $item);
}

    /**
     * Vista de demo de batería completado
     */
    public function batteryCompleted()
    {
        if (!Session::has('demo_mode') || Session::get('demo_type') !== 'battery') {
            return redirect()->route('admin.batteries.index');
        }

        $batteryId = Session::get('demo_battery_id');
        $battery = Battery::findOrFail($batteryId);
        $responses = Session::get('demo_responses', []);

        return view('admin.demo.battery-completed', compact('battery', 'responses'));
    }

    /**
     * MÉTODOS PRIVADOS ADICIONALES
     */

    private function determineNextActionBattery($task, $currentItem)
    {
        // Obtener todos los items de la tarea actual
        $allItems = $task->activeItems()->orderedByDifficulty()->get();

        // Buscar índice del item actual
        $currentIndex = $allItems->search(function($item) use ($currentItem) {
            return $item->id === $currentItem->id;
        });

        // Si hay siguiente item en esta tarea
        if ($currentIndex !== false && $currentIndex < $allItems->count() - 1) {
            $nextItem = $allItems[$currentIndex + 1];

            // Determinar URL según tipo de tarea
            $nextItemUrl = match($task->type) {
                TaskType::MATRIX => route('admin.demo.battery.matrix.item', ['itemId' => $nextItem->id]),
                TaskType::SPATIAL => route('admin.demo.battery.spatial.item', ['itemId' => $nextItem->id]),
                default => route('admin.demo.battery.task.show')
            };

            return response()->json([
                'success' => true,
                'next_item' => true,
                'next_item_url' => $nextItemUrl
            ]);
        }

        // Tarea completada, buscar siguiente tarea
        $batteryId = Session::get('demo_battery_id');
        $battery = Battery::with(['tasks' => function($query) {
            $query->orderBy('battery_tasks.order');
        }])->findOrFail($batteryId);

        $tasks = $battery->tasks;
        $currentTaskIndex = $tasks->search(function($t) use ($task) {
            return $t->id === $task->id;
        });

        // Buscar siguiente tarea con items activos
        $nextTask = null;
        for ($i = $currentTaskIndex + 1; $i < $tasks->count(); $i++) {
            if ($tasks[$i]->activeItems()->count() > 0) {
                $nextTask = $tasks[$i];
                break;
            }
        }

        if ($nextTask) {
            // Hay siguiente tarea
            Session::put('demo_current_task_id', $nextTask->id);

            return response()->json([
                'success' => true,
                'task_completed' => true,
                'next_task_url' => route('admin.demo.battery.task.show')
            ]);
        }

        // Batería completada
        return response()->json([
            'success' => true,
            'battery_completed' => true,
            'completion_url' => route('admin.demo.battery.completed')
        ]);
    }


    /**
     * MÉTODOS PRIVADOS
     */

    private function clearDemoSession()
    {
        Session::forget('demo_mode');
        Session::forget('demo_type');
        Session::forget('demo_task_id');
        Session::forget('demo_item_id');
        Session::forget('demo_battery_id');
        Session::forget('demo_current_task_id');
        Session::forget('demo_current_task_index');
        Session::forget('demo_responses');
        Session::forget('demo_started_at');
        Session::forget('demo_item_response');
    }

    private function determineNextAction($task, $currentItem)
{
    // Obtener todos los items de la tarea
    $allItems = $task->activeItems()->orderedByDifficulty()->get();

    // Buscar índice del item actual
    $currentIndex = $allItems->search(function($item) use ($currentItem) {
        return $item->id === $currentItem->id;
    });

    // Si hay siguiente item en esta tarea
    if ($currentIndex !== false && $currentIndex < $allItems->count() - 1) {
        $nextItem = $allItems[$currentIndex + 1];

        // Determinar URL según tipo de tarea
        $nextItemUrl = match($task->type) {
            TaskType::MATRIX => route('admin.demo.matrix.item', ['itemId' => $nextItem->id]),
            TaskType::SPATIAL => route('admin.demo.spatial.item', ['itemId' => $nextItem->id]),
            default => route('admin.demo.task.show')
        };

        return response()->json([
            'success' => true,
            'next_item' => true,
            'next_item_url' => $nextItemUrl
        ]);
    }

    // No hay más items, demo de tarea completado
    return response()->json([
        'success' => true,
        'demo_completed' => true,
        'completion_url' => route('admin.demo.completed')
    ]);
}
}
