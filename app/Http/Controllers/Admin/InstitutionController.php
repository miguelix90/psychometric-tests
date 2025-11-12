<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Enums\InstitutionType;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Institution::withCount(['users', 'participants']);

        // Filtro por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtro por usos disponibles
        if ($request->filled('uses_filter')) {
            switch ($request->uses_filter) {
                case 'low':
                    $query->where('available_uses', '<=', 10);
                    break;
                case 'medium':
                    $query->whereBetween('available_uses', [11, 50]);
                    break;
                case 'high':
                    $query->where('available_uses', '>', 50);
                    break;
                case 'none':
                    $query->where('available_uses', '<=', 0);
                    break;
            }
        }

        $institutions = $query->orderBy('name')->paginate(20);

        return view('admin.institutions.index', compact('institutions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = InstitutionType::cases();
        return view('admin.institutions.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:sociescuela,educativo,profesional,asociacion,others',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:institutions,email',
            'available_uses' => 'required|integer|min:0',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'type.required' => 'El tipo es obligatorio.',
            'contact_name.required' => 'El nombre de contacto es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser válido.',
            'email.unique' => 'Este email ya está registrado.',
            'available_uses.required' => 'Los usos disponibles son obligatorios.',
            'available_uses.integer' => 'Los usos disponibles deben ser un número.',
            'available_uses.min' => 'Los usos disponibles no pueden ser negativos.',
        ]);

        // El access_code se genera automáticamente por el modelo
        $institution = Institution::create($validated);

        return redirect()
            ->route('admin.institutions.index')
            ->with('success', "Institución creada correctamente. Código de acceso: {$institution->access_code}");
    }

    /**
     * Display the specified resource.
     */
    public function show(Institution $institution)
    {
        $institution->load(['users', 'participants']);

        // Obtener estadísticas
        $stats = [
            'total_users' => $institution->users()->count(),
            'total_participants' => $institution->participants()->count(),
            'pending_sessions' => $institution->testSessions()->pending()->count(),
            'in_progress_sessions' => $institution->testSessions()->inProgress()->count(),
            'completed_sessions' => $institution->testSessions()->completed()->count(),
        ];

        return view('admin.institutions.show', compact('institution', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Institution $institution)
    {
        $types = InstitutionType::cases();
        return view('admin.institutions.edit', compact('institution', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Institution $institution)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:sociescuela,educativo,profesional,asociacion,others',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:institutions,email,' . $institution->id,
            'available_uses' => 'required|integer|min:0',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'type.required' => 'El tipo es obligatorio.',
            'contact_name.required' => 'El nombre de contacto es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser válido.',
            'email.unique' => 'Este email ya está registrado.',
            'available_uses.required' => 'Los usos disponibles son obligatorios.',
            'available_uses.integer' => 'Los usos disponibles deben ser un número.',
            'available_uses.min' => 'Los usos disponibles no pueden ser negativos.',
        ]);

        $institution->update($validated);

        return redirect()
            ->route('admin.institutions.index')
            ->with('success', 'Institución actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institution $institution)
    {
        // Verificar que no tenga usuarios asociados
        if ($institution->users()->exists()) {
            return redirect()
                ->route('admin.institutions.index')
                ->with('error', 'No se puede eliminar la institución porque tiene usuarios asociados.');
        }

        // Verificar que no sea Sociescuela
        if ($institution->type === InstitutionType::SOCIESCUELA) {
            return redirect()
                ->route('admin.institutions.index')
                ->with('error', 'No se puede eliminar la institución Sociescuela.');
        }

        $institution->delete();

        return redirect()
            ->route('admin.institutions.index')
            ->with('success', 'Institución eliminada correctamente.');
    }

    /**
     * Add uses to institution
     */
    public function addUses(Request $request, Institution $institution)
    {
        $validated = $request->validate([
            'uses_to_add' => 'required|integer|min:1|max:10000',
        ], [
            'uses_to_add.required' => 'Debes especificar la cantidad de usos.',
            'uses_to_add.integer' => 'La cantidad debe ser un número entero.',
            'uses_to_add.min' => 'Debes agregar al menos 1 uso.',
            'uses_to_add.max' => 'No puedes agregar más de 10,000 usos a la vez.',
        ]);

        $institution->increment('available_uses', $validated['uses_to_add']);

        return redirect()
            ->route('admin.institutions.show', $institution)
            ->with('success', "Se agregaron {$validated['uses_to_add']} usos correctamente.");
    }
}
