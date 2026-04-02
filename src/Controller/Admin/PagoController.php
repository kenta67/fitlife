<?php

namespace App\Controller\Admin;

use App\Entity\Pago;
use App\Repository\PagoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/pagos')]
#[IsGranted('ROLE_ADMIN')]
class PagoController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/', name: 'admin_pagos_index', methods: ['GET'])]
    public function index(PagoRepository $repo, Request $request): Response
    {
        // Paginación simple
        $page = (int) $request->query->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $pagos = $repo->findBy([], ['fechaPago' => 'DESC'], $limit, $offset);
        $total = $repo->count([]);
        $totalPages = ceil($total / $limit);

        // Estadísticas simples
        $stats = [
            'total' => $total,
            // Puedes agregar más estadísticas aquí
        ];

        return $this->render('admin/pagos/index.html.twig', [
            'pagos' => $pagos,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPagos' => $total,
            'stats' => $stats,
        ]);
    }

    #[Route('/new/modal', name: 'admin_pagos_new_modal', methods: ['GET'])]
    public function newModal(): Response
    {
        // Formulario vacío para el modal
        $pago = new Pago();
        $form = $this->createForm(\App\Form\PagoType::class, $pago, [
            'action' => $this->generateUrl('admin_pagos_new'),
        ]);

        return $this->render('admin/pagos/_form_modal.html.twig', [
            'form' => $form->createView(),
            'title' => 'Registrar Pago',
            'action' => 'Registrar',
        ]);
    }

    #[Route('/new', name: 'admin_pagos_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $pago = new Pago();
        $form = $this->createForm(\App\Form\PagoType::class, $pago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($pago);
            $this->em->flush();
            $this->addFlash('success', 'Pago registrado correctamente.');
        } else {
            $this->addFlash('error', 'Error al registrar el pago. Verifique los datos.');
        }

        return $this->redirectToRoute('admin_pagos_index');
    }
}
