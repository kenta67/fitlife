<?php

namespace App\Controller\Admin;

use App\Repository\ClienteRepository;
use App\Repository\PagoRepository;
use App\Repository\MembresiaClienteRepository;
use App\Repository\PersonalRepository;
use App\Repository\ClaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/estadisticas')]
#[IsGranted('ROLE_ADMIN')]
class EstadisticasController extends AbstractController
{
    public function __construct(
        private ClienteRepository $clienteRepo,
        private PagoRepository $pagoRepo,
        private MembresiaClienteRepository $membresiaRepo,
        private PersonalRepository $personalRepo,
        private ClaseRepository $claseRepo
    ) {}

    #[Route('/', name: 'admin_estadisticas_index', methods: ['GET'])]
    public function index(): Response
    {
        $now = new \DateTime();
        $mesActual = $now->format('m');
        $anioActual = $now->format('Y');
        
        $mesAnterior = clone $now;
        $mesAnterior->modify('-1 month');

        // ── CLIENTES ──
        $clientesActivos = $this->clienteRepo->countActive();
        $clientesNuevosMes = $this->clienteRepo->countNewThisMonth();
        $clientesStats = $this->clienteRepo->countByStatus();

        // ── INGRESOS ──
        $ingresosMesActual = $this->pagoRepo->sumByMonth($anioActual, $mesActual);
        $ingresosMesAnterior = $this->pagoRepo->sumByMonth(
            $mesAnterior->format('Y'), 
            $mesAnterior->format('m')
        );
        $crecimientoIngresos = $ingresosMesAnterior > 0 
            ? (($ingresosMesActual - $ingresosMesAnterior) / $ingresosMesAnterior) * 100 
            : 0;

        // ── MEMBRESÍAS ──
        $membresiasVigentes = $this->membresiaRepo->countVigentes();

        // ── PERSONAL ──
        $personalActivo = $this->personalRepo->countActive();
        $personalPorRol = $this->personalRepo->countByRole();

        // ── CLASES ──
        $clasesStats = $this->claseRepo->countByStatus();
        $clasesTotal = $this->claseRepo->countAll();

        // ── DATOS PARA GRÁFICAS ──
        $ingresosDiarios = $this->pagoRepo->dailyIncome(30);
        $ingresosPorMetodo = $this->pagoRepo->sumByMetodoPago();

        return $this->render('admin/estadisticas/index.html.twig', [
            'resumen' => [
                'clientes_activos' => $clientesActivos,
                'clientes_nuevos_mes' => $clientesNuevosMes,
                'membresias_vigentes' => $membresiasVigentes,
                'personal_activo' => $personalActivo,
                'clases_activas' => $clasesStats['activas'],
                'ingresos_mes' => $ingresosMesActual,
                'crecimiento_porcentaje' => round($crecimientoIngresos, 1),
            ],
            'clientes' => $clientesStats,
            'personal' => $personalPorRol,
            'clases' => array_merge($clasesStats, ['total' => $clasesTotal]),
            'ingresos' => [
                'mes_actual' => $ingresosMesActual,
                'mes_anterior' => $ingresosMesAnterior,
                'por_metodo' => $ingresosPorMetodo,
            ],
            'grafica_ingresos_diarios' => json_encode($ingresosDiarios),
            'grafica_metodos' => json_encode(array_column($ingresosPorMetodo, 'total', 'metodo')),
        ]);
    }
}