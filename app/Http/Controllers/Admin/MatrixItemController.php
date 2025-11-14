<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MatrixItemController extends Controller
{
    /**
     * Display a listing of items for a specific Matrix task.
     */
    public function index(Task $task)
    {
        // Verificar que la tarea sea de tipo Matrix
        if (!$task->isMatrix()) {
            abort(404, 'Esta tarea no es de tipo Matrix.');
        }

        $items = $task->items()
            ->orderBy('difficulty')
            ->paginate(20);

        return view('admin.tasks.items.matrix.index', compact('task', 'items'));
    }

    /**
     * Show the form for creating a new Matrix item.
     */
    public function create(Task $task)
    {
        // Verificar que la tarea sea de tipo Matrix
        if (!$task->isMatrix()) {
            abort(404, 'Esta tarea no es de tipo Matrix.');
        }

        return view('admin.tasks.items.matrix.create', compact('task'));
    }

    /**
     * Store a newly created Matrix item in storage.
     */
    public function store(Request $request, Task $task)
    {
        // Verificar que la tarea sea de tipo Matrix
        if (!$task->isMatrix()) {
            abort(404, 'Esta tarea no es de tipo Matrix.');
        }

        $validated = $request->validate([
            'code' => 'required|string|unique:items,code|max:50',
            'difficulty' => 'required|numeric|min:0',
            'matrix_image' => 'required|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_1' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_2' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_3' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_4' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_5' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_6' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_7' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_8' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'correct_answer' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Preparar el contenido JSON
        $content = [];

        // Subir imagen de la matriz
        if ($request->hasFile('matrix_image')) {
            $matrixPath = $request->file('matrix_image')->store('items/matrices', 'public');
            $content['matrix_image'] = $matrixPath;
        }

        // Agregar opciones que tienen archivo (1-8 para Matrix)
        $content['options'] = [];
        for ($i = 1; $i <= 8; $i++) {
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
            ->route('admin.tasks.items.index', $task)
            ->with('success', 'Ítem Matrix creado correctamente.');
    }

    /**
     * Show the form for editing the specified Matrix item.
     */
    public function edit(Task $task, Item $item)
    {
        // Verificar que la tarea sea de tipo Matrix
        if (!$task->isMatrix()) {
            abort(404, 'Esta tarea no es de tipo Matrix.');
        }

        // Verificar que el item pertenece a esta tarea
        if ($item->task_id !== $task->id) {
            abort(404, 'Este ítem no pertenece a la tarea especificada.');
        }

        return view('admin.tasks.items.matrix.edit', compact('task', 'item'));
    }

    /**
     * Update the specified Matrix item in storage.
     */
    public function update(Request $request, Task $task, Item $item)
    {
        // Verificar que la tarea sea de tipo Matrix
        if (!$task->isMatrix()) {
            abort(404, 'Esta tarea no es de tipo Matrix.');
        }

        // Verificar que el item pertenece a esta tarea
        if ($item->task_id !== $task->id) {
            abort(404, 'Este ítem no pertenece a la tarea especificada.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:items,code,' . $item->id,
            'difficulty' => 'required|numeric|min:0',
            'matrix_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_1' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_2' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_3' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_4' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_5' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_6' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_7' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'option_8' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'delete_option_1' => 'nullable|boolean',
            'delete_option_2' => 'nullable|boolean',
            'delete_option_3' => 'nullable|boolean',
            'delete_option_4' => 'nullable|boolean',
            'delete_option_5' => 'nullable|boolean',
            'delete_option_6' => 'nullable|boolean',
            'delete_option_7' => 'nullable|boolean',
            'delete_option_8' => 'nullable|boolean',
            'correct_answer' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Preparar el contenido JSON (mantener el existente)
        $content = $item->content ?? [];

        // Actualizar imagen de la matriz si se sube una nueva
        if ($request->hasFile('matrix_image')) {
            // Eliminar la imagen anterior si existe
            if (isset($content['matrix_image']) && Storage::disk('public')->exists($content['matrix_image'])) {
                Storage::disk('public')->delete($content['matrix_image']);
            }
            $matrixPath = $request->file('matrix_image')->store('items/matrices', 'public');
            $content['matrix_image'] = $matrixPath;
        }

        // Manejar opciones con eliminación (1-8 para Matrix)
        if (!isset($content['options'])) {
            $content['options'] = [];
        }

        for ($i = 1; $i <= 8; $i++) {
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
            ->route('admin.tasks.items.index', $task)
            ->with('success', 'Ítem Matrix actualizado correctamente.');
    }

    /**
     * Remove the specified Matrix item from storage.
     */
    public function destroy(Task $task, Item $item)
    {
        // Verificar que la tarea sea de tipo Matrix
        if (!$task->isMatrix()) {
            abort(404, 'Esta tarea no es de tipo Matrix.');
        }

        // Verificar que el item pertenece a esta tarea
        if ($item->task_id !== $task->id) {
            abort(404, 'Este ítem no pertenece a la tarea especificada.');
        }

        // Eliminar las imágenes del storage
        if (isset($item->content['matrix_image']) && Storage::disk('public')->exists($item->content['matrix_image'])) {
            Storage::disk('public')->delete($item->content['matrix_image']);
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
            ->route('admin.tasks.items.index', $task)
            ->with('success', 'Ítem Matrix eliminado correctamente.');
    }
}
