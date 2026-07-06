<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            ['titulo' => 'Consulta general', 'descripcion' => 'Evaluación médica general y diagnóstico inicial.', 'precio' => 80, 'duracion' => '30 minutos', 'estado' => 'disponible'],
            ['titulo' => 'Consulta pediátrica', 'descripcion' => 'Atención médica especializada para niños y adolescentes.', 'precio' => 100, 'duracion' => '30 minutos', 'estado' => 'disponible'],
            ['titulo' => 'Control prenatal', 'descripcion' => 'Seguimiento del embarazo y salud materna.', 'precio' => 120, 'duracion' => '45 minutos', 'estado' => 'disponible'],
            ['titulo' => 'Odontología general', 'descripcion' => 'Revisión, limpieza y tratamiento dental básico.', 'precio' => 150, 'duracion' => '45 minutos', 'estado' => 'disponible'],
            ['titulo' => 'Ecografía abdominal', 'descripcion' => 'Estudio ecográfico de órganos abdominales.', 'precio' => 200, 'duracion' => '30 minutos', 'estado' => 'disponible'],
            ['titulo' => 'Análisis de laboratorio', 'descripcion' => 'Perfil de análisis clínicos de sangre y orina.', 'precio' => 90, 'duracion' => '20 minutos', 'estado' => 'disponible'],
            ['titulo' => 'Fisioterapia', 'descripcion' => 'Sesión de rehabilitación física.', 'precio' => 110, 'duracion' => '1 hora', 'estado' => 'disponible'],
            ['titulo' => 'Consulta dermatológica', 'descripcion' => 'Evaluación y tratamiento de afecciones de la piel.', 'precio' => 130, 'duracion' => '30 minutos', 'estado' => 'disponible'],
            ['titulo' => 'Electrocardiograma', 'descripcion' => 'Estudio de la actividad eléctrica del corazón.', 'precio' => 95, 'duracion' => '20 minutos', 'estado' => 'no'],
            ['titulo' => 'Nutrición y dietética', 'descripcion' => 'Consulta de valoración nutricional y plan alimenticio.', 'precio' => 85, 'duracion' => '40 minutos', 'estado' => 'disponible'],
        ];

        foreach ($servicios as $servicio) {
            Servicio::firstOrCreate(['titulo' => $servicio['titulo']], $servicio);
        }
    }
}
