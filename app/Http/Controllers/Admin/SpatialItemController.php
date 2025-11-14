<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpatialItemController extends Controller
{
    /**
     * Display a listing of items for a specific Spatial task.
     */
    public function index(Task $task)
    {
        // Verificar que la tarea sea de tipo Spatial
        if (!$task->isSpatial()) {
            abort(404, 'Esta tarea no es de tipo Spatial.');
        }

        $items = $task->items()
            ->orderBy('difficulty')
            ->paginate(20);

        return view('admin.tasks.items.spatial.index', compact('task', 'items'));
    }

    /**
     * Show the form for creating a new Spatial item.
     */
    public function create(Task $task)
    {
        // Verificar que la tarea sea de tipo Spatial
        if (!$task->isSpatial()) {
            abort(404, 'Esta tarea no es de tipo Spatial.');
        }

        return view('admin.tasks.items.spatial.create', compact('task'));
    }

    /**
     * Store a newly created Spatial item in storage.
     */
    public function store(Request $request, Task $task)
    {
        // Verificar que la tarea sea de tipo Spatial
        if (!$task->isSpatial()) {
            abort(404, 'Esta tarea no es de tipo Spatial.');
        }

        $validated = $request->validate([
            'code' => 'required|string|unique:items,code|max:50',
            'difficulty' => 'required|numeric|min:0',
            'question_text' => 'required|string|max:500',
            'stimulus_image' => 'required|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_1' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_2' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_3' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_4' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_5' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_6' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'correct_answer' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Preparar el contenido JSON
        $content = [];

        // Agregar texto de pregunta
        $content['question_text'] = $validated['question_text'];

        // Subir imagen de estímulo
        if ($request->hasFile('stimulus_image')) {
            $stimulusPath = $request->file('stimulus_image')->store('items/spatial', 'public');
            $content['stimulus_image'] = $stimulusPath;
        }

        // Agregar opciones que tienen archivo (1-6 para Spatial)
        $content['options'] = [];
        for ($i = 1; $i <= 6; $i++) {
            if ($request->hasFile("option_$i")) {
                $optionPath = $request->file("option_$i")->store('items/options', 'public');
                $content['options'][(string)$i] = $optionPath;
            }
        }

        // Crear el ítem
        Item::create([
            'task_id' => $task->id,
            'code' => $validated['code'],
            'difficulty' => $validated['difficulty'],
            'content' => $content,
            'correct_answer' => $validated['correct_answer'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.tasks.items.spatial.index', $task)
            ->with('success', 'Ítem Spatial creado correctamente.');
    }

    /**
     * Show the form for editing the specified Spatial item.
     */
    public function edit(Task $task, Item $item)
    {
        // Verificar que la tarea sea de tipo Spatial
        if (!$task->isSpatial()) {
            abort(404, 'Esta tarea no es de tipo Spatial.');
        }

        // Verificar que el item pertenece a esta tarea
        if ($item->task_id !== $task->id) {
            abort(404, 'Este ítem no pertenece a la tarea especificada.');
        }

        return view('admin.tasks.items.spatial.edit', compact('task', 'item'));
    }

    /**
     * Update the specified Spatial item in storage.
     */
    public function update(Request $request, Task $task, Item $item)
    {
        // Verificar que la tarea sea de tipo Spatial
        if (!$task->isSpatial()) {
            abort(404, 'Esta tarea no es de tipo Spatial.');
        }

        // Verificar que el item pertenece a esta tarea
        if ($item->task_id !== $task->id) {
            abort(404, 'Este ítem no pertenece a la tarea especificada.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:items,code,' . $item->id,
            'difficulty' => 'required|numeric|min:0',
            'question_text' => 'required|string|max:500',
            'stimulus_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_1' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_2' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_3' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_4' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_5' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_6' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'delete_option_1' => 'nullable|boolean',
            'delete_option_2' => 'nullable|boolean',
            'delete_option_3' => 'nullable|boolean',
            'delete_option_4' => 'nullable|boolean',
            'delete_option_5' => 'nullable|boolean',
            'delete_option_6' => 'nullable|boolean',
            'correct_answer' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Preparar el contenido JSON (mantener el existente)
        $content = $item->content ?? [];

        // Actualizar texto de pregunta
        $content['question_text'] = $validated['question_text'];

        // Actualizar imagen de estímulo si se sube una nueva
        if ($request->hasFile('stimulus_image')) {
            // Eliminar la imagen anterior si existe
            if (isset($content['stimulus_image']) && Storage::disk('public')->exists($content['stimulus_image'])) {
                Storage::disk('public')->delete($content['stimulus_image']);
            }
            $stimulusPath = $request->file('stimulus_image')->store('items/spatial', 'public');
            $content['stimulus_image'] = $stimulusPath;
        }

        // Manejar opciones con eliminación (1-6 para Spatial)
        if (!isset($content['options'])) {
            $content['options'] = [];
        }

        for ($i = 1; $i <= 6; $i++) {
            $deleteKey = "delete_option_$i";

            // Si se marcó para eliminar
            if ($request->has($deleteKey) && $request->input($deleteKey)) {
                // Eliminar archivo del storage
                if (isset($content['options'][(string)$i]) && Storage::disk('public')->exists($content['options'][(string)$i])) {
                    Storage::disk('public')->delete($content['options'][(string)$i]);
                }
                // Eliminar del array
                unset($content['options'][(string)$i]);
            }
            // Si se sube nueva imagen
            elseif ($request->hasFile("option_$i")) {
                // Eliminar la opción anterior si existe
                if (isset($content['options'][(string)$i]) && Storage::disk('public')->exists($content['options'][(string)$i])) {
                    Storage::disk('public')->delete($content['options'][(string)$i]);
                }
                $optionPath = $request->file("option_$i")->store('items/options', 'public');
                $content['options'][(string)$i] = $optionPath;
            }
        }

        // Actualizar el ítem
        $item->update([
            'code' => $validated['code'],
            'difficulty' => $validated['difficulty'],
            'content' => $content,
            'correct_answer' => $validated['correct_answer'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.tasks.items.spatial.index', $task)
            ->with('success', 'Ítem Spatial actualizado correctamente.');
    }

    /**
     * Remove the specified Spatial item from storage.
     */
    public function destroy(Task $task, Item $item)
    {
        // Verificar que la tarea sea de tipo Spatial
        if (!$task->isSpatial()) {
            abort(404, 'Esta tarea no es de tipo Spatial.');
        }

        // Verificar que el item pertenece a esta tarea
        if ($item->task_id !== $task->id) {
            abort(404, 'Este ítem no pertenece a la tarea especificada.');
        }

        // Eliminar las imágenes del storage
        if (isset($item->content['stimulus_image']) && Storage::disk('public')->exists($item->content['stimulus_image'])) {
            Storage::disk('public')->delete($item->content['stimulus_image']);
        }

        if (isset($item->content['options']) && is_array($item->content['options'])) {
            foreach ($item->content['options'] as $optionPath) {
                if (Storage::disk('public')->exists($optionPath)) {
                    Storage::disk('public')->delete($optionPath);
                }
            }
        }

        $item->delete();

        return redirect()
            ->route('admin.tasks.items.spatial.index', $task)
            ->with('success', 'Ítem Spatial eliminado correctamente.');
    }
}
