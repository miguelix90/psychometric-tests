<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\BatteryCode;
use App\Models\Battery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BatteryCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Query base
        $query = BatteryCode::with(['battery', 'institution', 'createdBy']);

        // Control de permisos
        if ($user->hasPermissionTo('batteries.view')) {
            // Administrador: ve TODOS los códigos
            if (!$user->hasRole('Administrador')) {
                // Responsable o Profesor: solo de su institución
                $query->where('institution_id', $user->institution_id);
            }
        } else {
            // Sin permisos
            abort(403, 'No tienes permisos para ver códigos de batería.');
        }

        // Filtros
        if ($request->filled('battery_id')) {
            $query->where('battery_id', $request->battery_id);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true)->where('expires_at', '>', now());
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where('expires_at', '<=', now());
                    break;
            }
        }

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $batteryCodes = $query->orderBy('created_at', 'desc')->paginate(20);

        // Obtener baterías para el filtro
        $batteries = Battery::where('is_active', true)->orderBy('name')->get();

        return view('professor.battery-codes.index', compact('batteryCodes', 'batteries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $batteries = Battery::where('is_active', true)->orderBy('name')->get();
        return view('professor.battery-codes.create', compact('batteries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'battery_id' => 'required|exists:batteries,id',
            'max_uses' => 'required|integer|min:1|max:1000',
        ], [
            'battery_id.required' => 'Debes seleccionar una batería.',
            'battery_id.exists' => 'La batería seleccionada no existe.',
            'max_uses.required' => 'El límite de usos es obligatorio.',
            'max_uses.integer' => 'El límite de usos debe ser un número entero.',
            'max_uses.min' => 'El límite de usos debe ser al menos 1.',
            'max_uses.max' => 'El límite de usos no puede ser mayor a 1000.',
        ]);

        $user = Auth::user();

        // Generar código único
        $code = BatteryCode::generateUniqueCode();

        // Calcular fecha de expiración (8 horas desde ahora)
        $expiresAt = Carbon::now()->addHours(8);

        // Crear código de batería
        $batteryCode = BatteryCode::create([
            'code' => $code,
            'battery_id' => $validated['battery_id'],
            'created_by_user_id' => $user->id,
            'institution_id' => $user->institution_id,
            'max_uses' => $validated['max_uses'],
            'current_uses' => 0,
            'is_active' => true,
            'expires_at' => $expiresAt,
        ]);

        // Generar URL completa
        $fullUrl = route('test.battery-code.form', ['code' => $code]);

        return redirect()
            ->route('professor.battery-codes.index')
            ->with('success', "Código creado exitosamente: {$code}")
            ->with('code_url', $fullUrl);
    }

    /**
     * Display the specified resource.
     */
    public function show(BatteryCode $batteryCode)
    {
        // Verificar permisos
        $user = Auth::user();

        if (!$user->hasRole('Administrador') && $batteryCode->institution_id !== $user->institution_id) {
            abort(403, 'No tienes permisos para ver este código.');
        }

        $batteryCode->load(['battery', 'institution', 'createdBy', 'testSessions']);

        return view('professor.battery-codes.show', compact('batteryCode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BatteryCode $batteryCode)
    {
        // Verificar permisos
        $user = Auth::user();

        if (!$user->hasRole('Administrador') && $batteryCode->institution_id !== $user->institution_id) {
            abort(403, 'No tienes permisos para editar este código.');
        }

        return view('professor.battery-codes.edit', compact('batteryCode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BatteryCode $batteryCode)
    {
        // Verificar permisos
        $user = Auth::user();

        if (!$user->hasRole('Administrador') && $batteryCode->institution_id !== $user->institution_id) {
            abort(403, 'No tienes permisos para editar este código.');
        }

        $validated = $request->validate([
            'max_uses' => 'required|integer|min:' . $batteryCode->current_uses . '|max:1000',
        ], [
            'max_uses.required' => 'El límite de usos es obligatorio.',
            'max_uses.integer' => 'El límite de usos debe ser un número entero.',
            'max_uses.min' => "El límite de usos no puede ser menor que los usos actuales ({$batteryCode->current_uses}).",
            'max_uses.max' => 'El límite de usos no puede ser mayor a 1000.',
        ]);

        $batteryCode->update([
            'max_uses' => $validated['max_uses'],
        ]);

        return redirect()
            ->route('professor.battery-codes.index')
            ->with('success', 'Límite de usos actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BatteryCode $batteryCode)
    {
        // Verificar permisos
        $user = Auth::user();

        if (!$user->hasRole('Administrador') && $batteryCode->institution_id !== $user->institution_id) {
            abort(403, 'No tienes permisos para eliminar este código.');
        }

        // Verificar que no tenga sesiones asociadas
        if ($batteryCode->testSessions()->exists()) {
            return redirect()
                ->route('professor.battery-codes.index')
                ->with('error', 'No se puede eliminar el código porque tiene sesiones asociadas. Puedes desactivarlo en su lugar.');
        }

        $batteryCode->delete();

        return redirect()
            ->route('professor.battery-codes.index')
            ->with('success', 'Código eliminado correctamente.');
    }

    /**
     * Deactivate a battery code manually
     */
    public function deactivate(BatteryCode $batteryCode)
    {
        // Verificar permisos
        $user = Auth::user();

        if (!$user->hasRole('Administrador') && $batteryCode->institution_id !== $user->institution_id) {
            abort(403, 'No tienes permisos para desactivar este código.');
        }

        $batteryCode->update(['is_active' => false]);

        return redirect()
            ->route('professor.battery-codes.index')
            ->with('success', 'Código desactivado correctamente.');
    }

    /**
     * Activate a battery code manually
     */
    public function activate(BatteryCode $batteryCode)
    {
        // Verificar permisos
        $user = Auth::user();

        if (!$user->hasRole('Administrador') && $batteryCode->institution_id !== $user->institution_id) {
            abort(403, 'No tienes permisos para activar este código.');
        }

        // Verificar que no esté expirado
        if ($batteryCode->isExpired()) {
            return redirect()
                ->route('professor.battery-codes.index')
                ->with('error', 'No se puede activar un código expirado.');
        }

        $batteryCode->update(['is_active' => true]);

        return redirect()
            ->route('professor.battery-codes.index')
            ->with('success', 'Código activado correctamente.');
    }
}
