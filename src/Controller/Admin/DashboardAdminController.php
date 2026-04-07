<?php

namespace App\Controller\Admin;

use App\Repository\ClienteRepository;
use App\Repository\MembresiaClienteRepository;
use App\Repository\PagoRepository;
use App\Repository\PersonalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
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
        private PersonalRepository $personalRepo,
        private EntityManagerInterface $entityManager
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

        // --- RESUMEN DE CLIENTES Y SUS MEMBRESIAS ---
        $resumen_clientes = [];
        // Traer todas las membresías vigentes con sus clientes y planes
        $membresias = $this->membresiaClienteRepo->createQueryBuilder('mc')
            ->leftJoin('mc.cliente', 'c')
            ->leftJoin('mc.plan', 'p')
            ->addSelect('c', 'p')
            ->where('mc.estado = :estado')
            ->setParameter('estado', true)
            ->getQuery()->getResult();

        foreach ($membresias as $mc) {
            $cliente = $mc->getCliente();
            $plan = $mc->getPlan();
            // Buscar la última clase inscrita (si existe)
            $claseNombre = '';
            if (method_exists($cliente, 'getId')) {
                $inscripcionClase = $this->entityManager
                    ->getRepository('App\\Entity\\InscripcionClase')
                    ->findOneBy(['cliente' => $cliente->getId()], ['fecha' => 'DESC']);
                if ($inscripcionClase && method_exists($inscripcionClase, 'getClase')) {
                    $clase = $inscripcionClase->getClase();
                    $claseNombre = $clase ? $clase->getNombre() : '';
                }
            }
            $resumen_clientes[] = [
                'cliente' => $cliente->getNombre() . ' ' . $cliente->getApellido(),
                'carnet' => $cliente->getCedula(),
                'fecha_inicio' => $mc->getFechaInicio()->format('d/m/Y'),
                'fecha_fin' => $mc->getFechaVencimiento()->format('d/m/Y'),
                'membresia' => $plan->getNombrePlan(),
                'clase' => $claseNombre,
            ];
        }

        return $this->render('admin/dashboard.html.twig', [
            'usuario' => $user,
            'stats' => [
                'clientes'   => $clientesActivos,
                'membresias' => $membresiasVigentes,
                'ingresos'   => $ingresosMes,
                'personal'   => $personalActivo,
            ],
            'resumen_clientes' => $resumen_clientes,
        ]);
    }
}
