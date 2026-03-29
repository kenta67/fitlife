<?php

namespace App\Controller\Recepcionista;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/recepcionista')]
#[IsGranted('ROLE_RECEPCIONISTA')]
class DashboardRecepcionistaController extends AbstractController
{
    #[Route('/dashboard', name: 'recepcionista_dashboard')]
    public function index(): Response
    {
        /** @var \App\Entity\Personal $user */
        $user = $this->getUser();

        return $this->render('recepcionista/dashboard.html.twig', [
            'usuario' => $user,
            'stats' => [
                'clientes_hoy'    => 14,
                'pagos_hoy'       => 7,
                'membresias_venc' => 5,
                'asistencias'     => 31,
            ],
        ]);
    }
}