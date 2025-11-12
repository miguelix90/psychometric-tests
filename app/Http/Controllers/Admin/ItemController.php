<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::with('task');

        // Filtrar por tarea si se especifica
        if ($request->has('task_id') && $request->task_id !== '') {
            $query->where('task_id', $request->task_id);
        }

        // Filtrar por estado activo/inactivo
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        // Búsqueda por código
        if ($request->has('search') && $request->search !== '') {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $items = $query->orderBy('task_id')->orderBy('difficulty')->paginate(20);
        $tasks = Task::orderBy('name')->get();

        return view('admin.items.index', compact('items', 'tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tasks = Task::orderBy('name')->get();
        return view('admin.items.create', compact('tasks'));
    }


    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $item->load('task');
        return view('admin.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $tasks = Task::orderBy('name')->get();
        return view('admin.items.edit', compact('item', 'tasks'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
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
            ->route('admin.items.index')
            ->with('success', 'Ítem eliminado correctamente.');
    }

    /**
 * Store a newly created resource in storage.
 */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'code' => 'required|string|unique:items,code|max:50',
            'difficulty' => 'required|numeric|min:0',
            'matrix_image' => 'required|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
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

        // Subir imagen de la matriz
        if ($request->hasFile('matrix_image')) {
            $matrixPath = $request->file('matrix_image')->store('items/matrices', 'public');
            $content['matrix_image'] = $matrixPath;
        }

        // ⭐ CORRECCIÓN: Solo agregar opciones que tienen archivo
        $content['options'] = [];
        for ($i = 1; $i <= 6; $i++) {
            if ($request->hasFile("option_$i")) {
                $optionPath = $request->file("option_$i")->store('items/options', 'public');
                $content['options'][(string)$i] = $optionPath; // Asegurar que la clave es string
            }
        }

        // Crear el ítem
        Item::create([
            'task_id' => $validated['task_id'],
            'code' => $validated['code'],
            'difficulty' => $validated['difficulty'],
            'content' => $content,
            'correct_answer' => $validated['correct_answer'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Ítem creado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'code' => 'required|string|max:50|unique:items,code,' . $item->id,
            'difficulty' => 'required|numeric|min:0',
            'matrix_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
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

        // Actualizar imagen de la matriz si se sube una nueva
        if ($request->hasFile('matrix_image')) {
            // Eliminar la imagen anterior si existe
            if (isset($content['matrix_image']) && Storage::disk('public')->exists($content['matrix_image'])) {
                Storage::disk('public')->delete($content['matrix_image']);
            }
            $matrixPath = $request->file('matrix_image')->store('items/matrices', 'public');
            $content['matrix_image'] = $matrixPath;
        }

        // ⭐ CORRECCIÓN: Manejar opciones con eliminación
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
            'task_id' => $validated['task_id'],
            'code' => $validated['code'],
            'difficulty' => $validated['difficulty'],
            'content' => $content,
            'correct_answer' => $validated['correct_answer'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Ítem actualizado correctamente.');
    }
}
