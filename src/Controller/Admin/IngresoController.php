<?php

namespace App\Controller\Admin;

use App\Repository\PagoRepository;
use App\Repository\ClienteRepository;
use App\Repository\MembresiaClienteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/ingresos')]
#[IsGranted('ROLE_ADMIN')]
class IngresoController extends AbstractController
{
    #[Route('/', name: 'admin_ingresos_index', methods: ['GET'])]
    public function index(PagoRepository $pagoRepo, Request $request): Response
    {
        // Filtros desde GET
        $fechaDesde = $request->query->get('fecha_desde');
        $fechaHasta = $request->query->get('fecha_hasta');
        $idBusqueda = $request->query->get('busqueda');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Datos filtrados
        $pagos = $pagoRepo->findWithFilters($offset, $limit, $fechaDesde, $fechaHasta, $idBusqueda ? (int)$idBusqueda : null);
        $total = $pagoRepo->countWithFilters($fechaDesde, $fechaHasta, $idBusqueda ? (int)$idBusqueda : null);
        $totalPages = ceil($total / $limit);

        // Estadísticas del filtro actual
        $ingresosTotales = $pagoRepo->sumByDateRange($fechaDesde, $fechaHasta);
        $porMetodo = $pagoRepo->sumByMetodoPago($fechaDesde, $fechaHasta);

        // Datos para gráfica (siempre últimos 30 días)
        $datosGrafica = $pagoRepo->dailyIncome(30);

        return $this->render('admin/ingresos/index.html.twig', [
            'pagos' => $pagos,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRegistros' => $total,
            'filtros' => [
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
                'busqueda' => $idBusqueda,
            ],
            'stats' => [
                'total_ingresos' => $ingresosTotales,
                'cantidad_pagos' => $total,
                'por_metodo' => $porMetodo,
            ],
            'datosGrafica' => json_encode($datosGrafica),
        ]);
    }

    #[Route('/exportar', name: 'admin_ingresos_exportar', methods: ['GET'])]
    public function exportar(PagoRepository $pagoRepo, Request $request): StreamedResponse
    {
        $fechaDesde = $request->query->get('fecha_desde');
        $fechaHasta = $request->query->get('fecha_hasta');

        $pagos = $pagoRepo->findAllForExport($fechaDesde, $fechaHasta);

        $response = new StreamedResponse(function() use ($pagos) {
            $handle = fopen('php://output', 'w');
            
            // Encabezados CSV
            fputcsv($handle, [
                'ID',
                'Fecha Pago',
                'Cliente',
                'Cédula',
                'Plan',
                'Monto (Bs)',
                'Método Pago',
                'Registrado Por'
            ], ';');

            foreach ($pagos as $pago) {
                fputcsv($handle, [
                    $pago->getId(),
                    $pago->getFechaPago()->format('d/m/Y'),
                    $pago->getMembresiaCliente()->getCliente()->getNombre() . ' ' . $pago->getMembresiaCliente()->getCliente()->getApellido(),
                    $pago->getMembresiaCliente()->getCliente()->getCedula(),
                    $pago->getMembresiaCliente()->getPlan()->getNombrePlan(),
                    number_format((float) $pago->getMonto(), 2, ',', '.'),
                    $pago->getMetodoPago(),
                    $pago->getPersonal()->getNombre(),
                ], ';');
            }

            fclose($handle);
        });

        $nombreArchivo = 'reporte_ingresos_' . date('Y-m-d_His') . '.csv';
        
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$nombreArchivo\"");
        
        return $response;
    }
}