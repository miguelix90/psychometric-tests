<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index()
    {
        $tasks = Task::withCount('items')->get();

        return view('admin.tasks.index', compact('tasks'));
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $task->load(['items' => function($query) {
            $query->orderBy('difficulty');
        }]);

        $activeItemsCount = $task->activeItems()->count();

        return view('admin.tasks.show', compact('task', 'activeItemsCount'));
    }
}
