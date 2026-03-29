<?php

namespace App\Controller\Instructor;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/instructor')]
#[IsGranted('ROLE_INSTRUCTOR')]
class DashboardInstructorController extends AbstractController
{
    #[Route('/dashboard', name: 'instructor_dashboard')]
    public function index(): Response
    {
        /** @var \App\Entity\Personal $user */
        $user = $this->getUser();

        return $this->render('instructor/dashboard.html.twig', [
            'usuario' => $user,
            'clases' => [
                ['nombre'=>'CrossFit',  'horario'=>'Lun-Mié-Vie 06:00', 'inscritos'=>18, 'max'=>20],
                ['nombre'=>'Spinning',  'horario'=>'Lun-Mié-Vie 18:00', 'inscritos'=>15, 'max'=>20],
                ['nombre'=>'HIIT',      'horario'=>'Sáb 08:00',         'inscritos'=> 9, 'max'=>15],
            ],
        ]);
    }
}