<?php

namespace App\Security;

// Archivo legado: el login activo ahora usa "form_login" en security.yaml.

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator) {}

    /**
     * Crea el Passport con las credenciales del formulario.
     * Lee los campos 'usuario' y 'contrasena' del POST.
     */
    public function authenticate(Request $request): Passport
    {
        $usuario = $request->getPayload()->getString('usuario');

        // Guarda el último usuario ingresado para mostrarlo si falla
        $request->getSession()->set(
            SecurityRequestAttributes::LAST_USERNAME,
            $usuario
        );

        return new Passport(
            new UserBadge($usuario),
            new PasswordCredentials($request->getPayload()->getString('contrasena')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * Se ejecuta tras un login exitoso.
     * Redirige según el rol del usuario autenticado.
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        // Si venía de una URL protegida, regresa a ella
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        $roles = $token->getUser()->getRoles();

        return match(true) {
            in_array('ROLE_ADMIN', $roles)         => new RedirectResponse(
                $this->urlGenerator->generate('admin_dashboard')
            ),
            in_array('ROLE_RECEPCIONISTA', $roles) => new RedirectResponse(
                $this->urlGenerator->generate('recepcionista_dashboard')
            ),
            in_array('ROLE_INSTRUCTOR', $roles)    => new RedirectResponse(
                $this->urlGenerator->generate('instructor_dashboard')
            ),
            default => new RedirectResponse(
                $this->urlGenerator->generate('app_home')
            ),
        };
    }

    /**
     * URL a la que se redirige si se intenta acceder a una ruta protegida
     * sin estar autenticado.
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
