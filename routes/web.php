<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de administrador (protegidas por middleware auth y rol)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Items CRUD
    Route::resource('items', App\Http\Controllers\Admin\ItemController::class);
    Route::post('items/preview', [App\Http\Controllers\Admin\ItemController::class, 'preview'])
        ->name('items.preview');

    // Tasks (solo index y show, no CRUD completo)
    Route::get('tasks', [App\Http\Controllers\Admin\TaskController::class, 'index'])->name('tasks.index');
    Route::get('tasks/{task}', [App\Http\Controllers\Admin\TaskController::class, 'show'])->name('tasks.show');

    // Batteries CRUD
    Route::resource('batteries', App\Http\Controllers\Admin\BatteryController::class);

    // Institutions CRUD
    Route::resource('institutions', App\Http\Controllers\Admin\InstitutionController::class);

    // Agregar usos a institución
    Route::post('institutions/{institution}/add-uses', [App\Http\Controllers\Admin\InstitutionController::class, 'addUses'])
        ->name('institutions.add-uses');


});

// Rutas de profesor (protegidas por middleware auth)
Route::middleware(['auth'])->prefix('professor')->name('professor.')->group(function () {
    // Participants CRUD
    Route::resource('participants', App\Http\Controllers\Professor\ParticipantController::class);

    // Battery Codes CRUD
    Route::resource('battery-codes', App\Http\Controllers\Professor\BatteryCodeController::class);

    // Acciones adicionales de battery-codes
    Route::post('battery-codes/{batteryCode}/deactivate', [App\Http\Controllers\Professor\BatteryCodeController::class, 'deactivate'])
        ->name('battery-codes.deactivate');

    Route::post('battery-codes/{batteryCode}/activate', [App\Http\Controllers\Professor\BatteryCodeController::class, 'activate'])
        ->name('battery-codes.activate');

    Route::post('test-sessions/{testSession}/cancel', [App\Http\Controllers\Professor\TestSessionController::class, 'cancel'])
        ->name('test-sessions.cancel');

    // Asignar batería a participante
    Route::get('participants/{participant}/assign-battery', [App\Http\Controllers\Professor\ParticipantController::class, 'assignBattery'])
        ->name('participants.assign-battery');

    Route::post('participants/{participant}/assign-battery', [App\Http\Controllers\Professor\ParticipantController::class, 'storeAssignment'])
        ->name('participants.store-assignment');

    // Test Sessions Dashboard
    Route::get('test-sessions', [App\Http\Controllers\Professor\TestSessionController::class, 'index'])
        ->name('test-sessions.index');

    // Cancelar sesión
    Route::post('test-sessions/{testSession}/cancel', [App\Http\Controllers\Professor\TestSessionController::class, 'cancel'])
        ->name('test-sessions.cancel');
});

// ====================================================================
// RUTAS PÚBLICAS PARA PARTICIPANTES (SIN AUTENTICACIÓN)
// ====================================================================

// Ruta de acceso con código de institución
Route::get('/acceso_cuestionario/{access_code}', [App\Http\Controllers\ParticipantAccessController::class, 'showAccessForm'])
    ->name('participant.access.form');

// Validar IUC y dar acceso
Route::post('/acceso_cuestionario/{access_code}', [App\Http\Controllers\ParticipantAccessController::class, 'validateAccess'])
    ->name('participant.access.validate');

// Rutas protegidas para participantes (con sesión, sin autenticación)
Route::middleware(['participant.session'])->prefix('participante')->name('participant.')->group(function () {
    // Dashboard del participante
    Route::get('/inicio', [App\Http\Controllers\ParticipantAccessController::class, 'dashboard'])
        ->name('dashboard');

    // Cerrar sesión
    Route::post('/salir', [App\Http\Controllers\ParticipantAccessController::class, 'logout'])
        ->name('logout');
});

// Rutas de sesiones de test (comunes a todos los tipos)
Route::middleware(['participant.session'])->prefix('test')->name('test.')->group(function () {
    // Ver sesión de test
    Route::get('/session/{testSession}', [App\Http\Controllers\Test\TestSessionController::class, 'show'])
        ->name('session.show');

    // Iniciar sesión
    Route::post('/session/{testSession}/start', [App\Http\Controllers\Test\TestSessionController::class, 'start'])
        ->name('session.start');

    // Completar sesión
    Route::post('/session/{testSession}/complete', [App\Http\Controllers\Test\TestSessionController::class, 'complete'])
        ->name('session.complete');
});

// Rutas públicas para acceso por código de batería
Route::get('/test/{code}', [App\Http\Controllers\TestAccessController::class, 'showBatteryCodeForm'])
    ->name('test.battery-code.form');

Route::post('/test/{code}', [App\Http\Controllers\TestAccessController::class, 'validateBatteryCodeAccess'])
    ->name('test.battery-code.validate');

// Rutas de Demo (Solo Administradores) - Por Tarea
Route::middleware(['auth'])->prefix('admin/demo')->name('admin.demo.')->group(function () {
    Route::get('/task/{task}/start', [App\Http\Controllers\Admin\DemoController::class, 'start'])->name('start');
    Route::get('/task', [App\Http\Controllers\Admin\DemoController::class, 'showTask'])->name('task.show');
    Route::post('/task/start', [App\Http\Controllers\Admin\DemoController::class, 'startTask'])->name('task.start');

    // Matrix
    Route::get('/matrix/item/{itemId}', [App\Http\Controllers\Admin\DemoController::class, 'showMatrixItem'])->name('matrix.item');
    Route::post('/matrix/item/{itemId}/response', [App\Http\Controllers\Admin\DemoController::class, 'submitMatrixResponse'])->name('matrix.response');

    // Demo de ITEM Individual (Nivel 1) ← AGREGAR AQUÍ
    Route::get('/item/{item}/start', [App\Http\Controllers\Admin\DemoController::class, 'startItem'])->name('item.start');
    Route::get('/item/matrix/{item}', [App\Http\Controllers\Admin\DemoController::class, 'showItemMatrix'])->name('item.matrix');
    Route::post('/item/{item}/response', [App\Http\Controllers\Admin\DemoController::class, 'submitItemResponse'])->name('item.response');

    // Demo de BATERÍA Completa (Nivel 3) ← AGREGAR AQUÍ
    Route::get('/battery/{battery}/start', [App\Http\Controllers\Admin\DemoController::class, 'startBattery'])->name('battery.start');
    Route::get('/battery', [App\Http\Controllers\Admin\DemoController::class, 'showBattery'])->name('battery.show');
    Route::post('/battery/start', [App\Http\Controllers\Admin\DemoController::class, 'startBatteryExecution'])->name('battery.start.execution');
    Route::get('/battery/task', [App\Http\Controllers\Admin\DemoController::class, 'showBatteryTask'])->name('battery.task.show');
    Route::post('/battery/task/start', [App\Http\Controllers\Admin\DemoController::class, 'startBatteryTask'])->name('battery.task.start');
    Route::get('/battery/matrix/item/{itemId}', [App\Http\Controllers\Admin\DemoController::class, 'showBatteryMatrixItem'])->name('battery.matrix.item');
    Route::post('/battery/item/{itemId}/response', [App\Http\Controllers\Admin\DemoController::class, 'submitBatteryResponse'])->name('battery.response');
    Route::get('/battery/completed', [App\Http\Controllers\Admin\DemoController::class, 'batteryCompleted'])->name('battery.completed');

    // Control
    Route::post('/reset', [App\Http\Controllers\Admin\DemoController::class, 'reset'])->name('reset');
    Route::post('/exit', [App\Http\Controllers\Admin\DemoController::class, 'exit'])->name('exit');
    Route::get('/completed', [App\Http\Controllers\Admin\DemoController::class, 'completed'])->name('completed');
});

require __DIR__.'/auth.php';

/*RUTAS ESPECIFICAS POR TIPO DE TEST*/
require __DIR__.'/test-types/matrix.php';
