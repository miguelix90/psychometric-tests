<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Enums\TaskType;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            [
                'name' => 'Matrices',
                'type' => TaskType::MATRIX->value,
                'description' => 'Tarea de razonamiento abstracto mediante matrices progresivas. Evalúa la capacidad de razonamiento fluido y la habilidad para identificar patrones y relaciones entre elementos visuales.',
                'instructions' => 'En cada pantalla verás una matriz con un hueco. Debes identificar qué pieza completa correctamente el patrón. Observa cuidadosamente las filas y columnas para descubrir la regla que sigue la secuencia. Selecciona la opción que mejor complete la matriz.'
            ],
            [
                'name' => 'Aptitud numérica',
                'type' => TaskType::MATRIX->value,
                'description' => 'Tarea que evalúa las habilidades de cálculo mental, razonamiento cuantitativo y resolución de problemas numéricos. Mide la capacidad para trabajar con números y conceptos matemáticos.',
                'instructions' => 'Se te presentarán problemas matemáticos que deberás resolver. Lee cuidadosamente cada problema y calcula la respuesta correcta. Puedes usar papel y lápiz si lo necesitas, pero no calculadora. Trabaja con precisión y revisa tus cálculos antes de responder.'
            ],
            [
                'name' => 'Memoria',
                'type' => TaskType::MATRIX->value,
                'description' => 'Tarea que evalúa diferentes aspectos de la memoria de trabajo y la capacidad de retener y manipular información a corto plazo. Incluye memoria verbal y memoria visual.',
                'instructions' => 'Se te mostrará información que deberás recordar. Presta mucha atención durante la fase de presentación. Después se te pedirá que recuerdes esa información. Concéntrate y trata de crear asociaciones mentales que te ayuden a recordar.'
            ],
            [
                'name' => 'Velocidad de procesamiento',
                'type' => TaskType::SELECTION->value,
                'description' => 'Tarea que mide la rapidez y precisión con la que puedes procesar información visual simple. Evalúa la atención, la coordinación visomotora y la capacidad de realizar tareas cognitivas de forma rápida.',
                'instructions' => 'Deberás completar tareas simples lo más rápido posible sin cometer errores. La velocidad es importante, pero también lo es la precisión. Trabaja de forma rápida pero cuidadosa. No te detengas demasiado en ningún elemento.'
            ],
            [
                'name' => 'Visoespacial',
                'type' => TaskType::MATRIX->SPATIAL,
                'description' => 'Tarea que evalúa la capacidad de percibir, analizar y manipular mentalmente objetos en el espacio. Mide las habilidades de rotación mental, visualización espacial y relaciones espaciales.',
                'instructions' => 'Se te presentarán figuras y formas que deberás analizar espacialmente. Algunas tareas requerirán que imagines cómo se verían los objetos desde diferentes perspectivas o cómo encajarían diferentes piezas. Visualiza mentalmente las transformaciones antes de responder.'
            ]
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
