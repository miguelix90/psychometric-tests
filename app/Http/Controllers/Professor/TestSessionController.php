<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\TestSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TestSessionController extends Controller
{
    /**
     * Cancelar una sesión de test (marcar como abandonada y recuperar uso)
     */
    public function cancel(TestSession $testSession)
    {
        $user = Auth::user();

        // Verificar permisos
        if (!$user->hasRole('Administrador')) {
            // Verificar que pertenece a la misma institución
            if ($testSession->institution_id !== $user->institution_id) {
                abort(403, 'No tienes permisos para cancelar esta sesión.');
            }
        }

        // Verificar que la sesión puede ser cancelada (pending o in_progress)
        if (!$testSession->isPending() && !$testSession->isInProgress()) {
            return back()->with('error', 'Solo se pueden cancelar sesiones pendientes o en progreso.');
        }

        // Cancelar sesión (marca como abandonada y recupera uso)
        $testSession->cancel();

        // Si fue por código, decrementar el contador del código
        if ($testSession->battery_code_id) {
            $testSession->batteryCode->decrementUses();
        }

        return back()->with('success', 'Sesión cancelada correctamente. Se ha recuperado el uso.');
    }

    /**
     * Display a listing of test sessions
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Query base con relaciones
        $query = TestSession::with(['participant', 'battery', 'assignedBy', 'batteryCode'])
            ->recent();

        // Control de permisos
        if ($user->hasRole('Administrador')) {
            // Admin ve todas las sesiones
        } elseif ($user->hasRole('Responsable')) {
            // Responsable ve todas de su institución
            $query->forInstitution($user->institution_id);
        } else {
            // Profesor ve solo las que él asignó O las de participantes que él creó
            $query->where(function($q) use ($user) {
                $q->where('assigned_by_user_id', $user->id)
                ->orWhereHas('participant', function($subQ) use ($user) {
                    $subQ->where('created_by_user_id', $user->id);
                });
            });
        }

        // Filtros

        // Filtro por estado
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->pending();
                    break;
                case 'in_progress':
                    $query->inProgress();
                    break;
                case 'completed':
                    $query->completed();
                    break;
                case 'abandoned':
                    $query->abandoned();
                    break;
                case 'active':
                    $query->active();
                    break;
            }
        }

        // Filtro por batería
        if ($request->filled('battery_id')) {
            $query->forBattery($request->battery_id);
        }

        // Filtro por participante (búsqueda por IUC)
        if ($request->filled('search')) {
            $query->whereHas('participant', function($q) use ($request) {
                $q->where('iuc', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por forma de asignación
        if ($request->filled('assignment_type')) {
            if ($request->assignment_type === 'direct') {
                $query->whereNull('battery_code_id');
            } elseif ($request->assignment_type === 'code') {
                $query->whereNotNull('battery_code_id');
            }
        }

        // Filtro por fecha
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sessions = $query->paginate(20);

        // Obtener baterías para el filtro
        $batteries = \App\Models\Battery::where('is_active', true)->orderBy('name')->get();

        // Estadísticas
        $stats = [
            'total' => TestSession::query()
                ->when(!$user->hasRole('Administrador'), function($q) use ($user) {
                    if ($user->hasRole('Responsable')) {
                        $q->forInstitution($user->institution_id);
                    } else {
                        $q->where(function($subQ) use ($user) {
                            $subQ->where('assigned_by_user_id', $user->id)
                                ->orWhereHas('participant', function($pQ) use ($user) {
                                    $pQ->where('created_by_user_id', $user->id);
                                });
                        });
                    }
                })
                ->count(),
            'pending' => TestSession::query()
                ->when(!$user->hasRole('Administrador'), function($q) use ($user) {
                    if ($user->hasRole('Responsable')) {
                        $q->forInstitution($user->institution_id);
                    } else {
                        $q->where(function($subQ) use ($user) {
                            $subQ->where('assigned_by_user_id', $user->id)
                                ->orWhereHas('participant', function($pQ) use ($user) {
                                    $pQ->where('created_by_user_id', $user->id);
                                });
                        });
                    }
                })
                ->pending()
                ->count(),
            'in_progress' => TestSession::query()
                ->when(!$user->hasRole('Administrador'), function($q) use ($user) {
                    if ($user->hasRole('Responsable')) {
                        $q->forInstitution($user->institution_id);
                    } else {
                        $q->where(function($subQ) use ($user) {
                            $subQ->where('assigned_by_user_id', $user->id)
                                ->orWhereHas('participant', function($pQ) use ($user) {
                                    $pQ->where('created_by_user_id', $user->id);
                                });
                        });
                    }
                })
                ->inProgress()
                ->count(),
            'completed' => TestSession::query()
                ->when(!$user->hasRole('Administrador'), function($q) use ($user) {
                    if ($user->hasRole('Responsable')) {
                        $q->forInstitution($user->institution_id);
                    } else {
                        $q->where(function($subQ) use ($user) {
                            $subQ->where('assigned_by_user_id', $user->id)
                                ->orWhereHas('participant', function($pQ) use ($user) {
                                    $pQ->where('created_by_user_id', $user->id);
                                });
                        });
                    }
                })
                ->completed()
                ->count(),
        ];

        return view('professor.test-sessions.index', compact('sessions', 'batteries', 'stats'));
    }
}
