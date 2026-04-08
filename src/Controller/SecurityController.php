<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si ya hay sesion abierta, no volvemos a mostrar el formulario.
        if ($this->getUser()) {
            return $this->redirectToDashboard();
        }

        $error        = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/redirigir-login', name: 'app_login_redirect')]
    public function loginRedirect(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToDashboard();
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony intercepta esta ruta; el metodo existe solo para declarar la ruta.
        throw new \LogicException('Gestionado por el firewall de Symfony.');
    }

    /** Punto unico para decidir a que panel entra cada usuario. */
    private function redirectToDashboard(): Response
    {
        $roles = $this->getUser()->getRoles();

        if (in_array('ROLE_ADMIN', $roles)) {
            return $this->redirectToRoute('admin_dashboard');
        }
        if (in_array('ROLE_RECEPCIONISTA', $roles)) {
            return $this->redirectToRoute('recepcionista_dashboard');
        }
        if (in_array('ROLE_INSTRUCTOR', $roles)) {
            return $this->redirectToRoute('instructor_dashboard');
        }

        return $this->redirectToRoute('app_home');
    }
}
