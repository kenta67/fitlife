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
        // Si ya está autenticado, redirige a su dashboard correspondiente
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

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Este método nunca se ejecuta directamente.
        // Symfony intercepta /logout según lo configurado en security.yaml.
        throw new \LogicException('Gestionado por el firewall de Symfony.');
    }

    /** Redirige al dashboard según el rol del usuario actual */
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