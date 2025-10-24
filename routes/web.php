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

require __DIR__.'/auth.php';
