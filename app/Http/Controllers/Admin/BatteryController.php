<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Battery;
use App\Models\Task;
use App\Enums\BatteryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatteryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Battery::query()->with('tasks');

        // Filtro por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtro por estado (activo/inactivo)
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $batteries = $query->orderBy('name')->paginate(20);

        return view('admin.batteries.index', compact('batteries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = BatteryType::cases();
        return view('admin.batteries.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:screening,complete,validation',
            'has_scoring' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Establecer has_scoring según el tipo si no viene en el request
        if (!isset($validated['has_scoring'])) {
            $type = BatteryType::from($validated['type']);
            $validated['has_scoring'] = $type->hasScoring();
        }

        $validated['is_active'] = $request->has('is_active');

        $battery = Battery::create($validated);

        return redirect()
            ->route('admin.batteries.edit', $battery)
            ->with('success', 'Batería creada exitosamente. Ahora puedes asignar tareas.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Battery $battery)
    {
        $battery->load('tasks');
        return view('admin.batteries.show', compact('battery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Battery $battery)
    {
        $battery->load('tasks');
        $types = BatteryType::cases();
        $availableTasks = Task::all();

        return view('admin.batteries.edit', compact('battery', 'types', 'availableTasks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Battery $battery)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:screening,complete,validation',
            'has_scoring' => 'boolean',
            'is_active' => 'boolean',
            'tasks' => 'nullable|array',
            'tasks.*' => 'exists:tasks,id',
            'task_orders' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Actualizar has_scoring según el tipo si no viene en el request
        if (!isset($validated['has_scoring'])) {
            $type = BatteryType::from($validated['type']);
            $validated['has_scoring'] = $type->hasScoring();
        }

        DB::transaction(function () use ($battery, $validated, $request) {
            // Actualizar la batería
            $battery->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'has_scoring' => $validated['has_scoring'],
                'is_active' => $validated['is_active'],
            ]);

            // Sincronizar tareas con su orden
            if (isset($validated['tasks'])) {
                $syncData = [];
                foreach ($validated['tasks'] as $taskId) {
                    $order = $request->input("task_orders.{$taskId}", 0);
                    $syncData[$taskId] = ['order' => $order];
                }
                $battery->tasks()->sync($syncData);
            } else {
                $battery->tasks()->detach();
            }
        });

        return redirect()
            ->route('admin.batteries.index')
            ->with('success', 'Batería actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Battery $battery)
    {
        $battery->delete();

        return redirect()
            ->route('admin.batteries.index')
            ->with('success', 'Batería eliminada exitosamente.');
    }
}
