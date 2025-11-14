<?php

use Illuminate\Support\Facades\Route;
use App\Tests\Spatial\Controllers\SpatialTestController;

/*
|--------------------------------------------------------------------------
| Rutas de Spatial Test
|--------------------------------------------------------------------------
|
| Rutas específicas para la ejecución de tareas de tipo Spatial (Visoespacial)
| Estas rutas son accedidas por participantes durante la ejecución de tests
|
*/

Route::middleware(['participant.session'])->prefix('test/spatial')->name('test.spatial.')->group(function () {

    // Mostrar instrucciones de la tarea
    Route::get('/task/{testSessionTask}', [SpatialTestController::class, 'showTask'])
        ->name('task');

    // Mostrar item específico
    Route::get('/task/{testSessionTask}/item/{item}', [SpatialTestController::class, 'showItem'])
        ->name('item');

    // Enviar respuesta de item
    Route::post('/task/{testSessionTask}/item/{item}/submit', [SpatialTestController::class, 'submitResponse'])
        ->name('item.submit');
});
