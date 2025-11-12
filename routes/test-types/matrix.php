<?php

use App\Tests\Matrix\Controllers\MatrixTestController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas específicas para tests de tipo MATRIX
 * Protegidas por middleware participant.session
 */

Route::middleware(['participant.session'])->prefix('test/matrix')->name('test.matrix.')->group(function () {

    // Ver tarea
    Route::get('/session/{testSession}/task/{testSessionTask}', [MatrixTestController::class, 'showTask'])
        ->name('task');

    // Iniciar tarea
    Route::post('/task/{testSessionTask}/start', [MatrixTestController::class, 'startTask'])
        ->name('task.start');

    // Ver item específico
    Route::get('/task/{testSessionTask}/item/{item}', [MatrixTestController::class, 'showItem'])
        ->name('item.show');

    // Enviar respuesta (AJAX)
    Route::post('/task/{testSessionTask}/item/{item}/response', [MatrixTestController::class, 'submitResponse'])
        ->name('item.submit');

    // Completar tarea
    Route::post('/task/{testSessionTask}/complete', [MatrixTestController::class, 'completeTask'])
        ->name('task.complete');
});
