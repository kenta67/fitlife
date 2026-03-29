<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $planes = [
            [
                'nombre'   => 'Plan Básico',
                'precio'   => '49',
                'duracion' => '30 días',
                'features' => ['Acceso sala principal', 'Vestuarios', 'Casillero'],
                'destacado' => false,
            ],
            [
                'nombre'   => 'Plan Pro',
                'precio'   => '89',
                'duracion' => '30 días',
                'features' => ['Todo Plan Básico', 'Clases grupales', 'Nutricionista'],
                'destacado' => true,
            ],
            [
                'nombre'   => 'Plan Elite',
                'precio'   => '139',
                'duracion' => '30 días',
                'features' => ['Todo Plan Pro', 'Entrenador personal', 'SPA incluido'],
                'destacado' => false,
            ],
        ];

        $clases = [
            ['nombre' => 'CrossFit',    'horario' => 'Lun-Mié-Vie 06:00', 'icono' => '🔥'],
            ['nombre' => 'Yoga',         'horario' => 'Mar-Jue 07:00',     'icono' => '🧘'],
            ['nombre' => 'Spinning',     'horario' => 'Lun-Mié-Vie 18:00', 'icono' => '🚴'],
            ['nombre' => 'Boxeo',        'horario' => 'Mar-Jue-Sáb 19:00', 'icono' => '🥊'],
            ['nombre' => 'Pilates',      'horario' => 'Mar-Jue 08:00',     'icono' => '🌿'],
            ['nombre' => 'Zumba',        'horario' => 'Sáb-Dom 09:00',     'icono' => '💃'],
        ];

        return $this->render('home/index.html.twig', [
            'planes' => $planes,
            'clases' => $clases,
        ]);
    }
}