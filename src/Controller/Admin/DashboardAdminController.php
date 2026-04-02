<?php

namespace App\Controller\Admin;

use App\Repository\ClienteRepository;
use App\Repository\MembresiaClienteRepository;
use App\Repository\PagoRepository;
use App\Repository\PersonalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use DateTimeImmutable;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardAdminController extends AbstractController
{
    public function __construct(
        private ClienteRepository $clienteRepo,
        private MembresiaClienteRepository $membresiaClienteRepo,
        private PagoRepository $pagoRepo,
        private PersonalRepository $personalRepo
    ) {}

    #[Route('/dashboard', name: 'admin_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();

        // 1. Clientes activos
        $clientesActivos = $this->clienteRepo->countActive();

        // 2. Membresías vigentes (estado = true y fechaVencimiento >= hoy)
        $membresiasVigentes = $this->membresiaClienteRepo->countVigentes();

        // 3. Ingresos del mes actual (pagos confirmados)
        $now = new DateTimeImmutable();
        $ingresosMes = $this->pagoRepo->sumByMonth($now->format('Y'), $now->format('m'));

        // 4. Personal activo
        $personalActivo = $this->personalRepo->countActive();

        return $this->render('admin/dashboard.html.twig', [
            'usuario' => $user,
            'stats' => [
                'clientes'   => $clientesActivos,
                'membresias' => $membresiasVigentes,
                'ingresos'   => $ingresosMes,
                'personal'   => $personalActivo,
            ],
        ]);
    }
}