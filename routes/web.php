<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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

    // Batteries CRUD
    Route::resource('batteries', App\Http\Controllers\Admin\BatteryController::class);
});

// Rutas de profesor (protegidas por middleware auth)
Route::middleware(['auth'])->prefix('professor')->name('professor.')->group(function () {
    // Participants CRUD
    Route::resource('participants', App\Http\Controllers\Professor\ParticipantController::class);
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

require __DIR__.'/auth.php';
