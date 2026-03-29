<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardAdminController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function index(): Response
    {
        /** @var \App\Entity\Personal $user */
        $user = $this->getUser();

        return $this->render('admin/dashboard.html.twig', [
            'usuario' => $user,
            'stats' => [
                'clientes'   => 248,   // En producción: consulta real a BD
                'membresias' => 186,
                'ingresos'   => 18430,
                'personal'   => 12,
            ],
        ]);
    }
}