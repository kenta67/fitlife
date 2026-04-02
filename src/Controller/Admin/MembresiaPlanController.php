<?php

namespace App\Controller\Admin;

use App\Entity\MembresiaPlan;
use App\Form\MembresiaPlanType;
use App\Repository\MembresiaPlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/planes')]
#[IsGranted('ROLE_ADMIN')]
class MembresiaPlanController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/', name: 'admin_planes_index', methods: ['GET'])]
    public function index(MembresiaPlanRepository $repo, Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $planes = $repo->findPaginated($offset, $limit);
        $total = $repo->countAll();
        $totalPages = ceil($total / $limit);
        $stats = $repo->countByStatus();

        return $this->render('admin/planes/index.html.twig', [
            'planes' => $planes,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPlanes' => $total,
            'stats' => $stats,
        ]);
    }

    #[Route('/new', name: 'admin_planes_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $plan = new MembresiaPlan();
        $form = $this->createForm(MembresiaPlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($plan);
            $this->em->flush();
            $this->addFlash('success', 'Plan creado correctamente.');
            return $this->redirectToRoute('admin_planes_index');
        }

        return $this->render('admin/planes/new.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nuevo Plan',
            'action' => 'Crear',
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_planes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MembresiaPlan $plan): Response
    {
        $form = $this->createForm(MembresiaPlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Plan actualizado correctamente.');
            return $this->redirectToRoute('admin_planes_index');
        }

        return $this->render('admin/planes/edit.html.twig', [
            'form' => $form->createView(),
            'plan' => $plan,
            'title' => 'Editar Plan',
            'action' => 'Actualizar',
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_planes_delete', methods: ['POST'])]
    public function delete(Request $request, MembresiaPlan $plan): Response
    {
        if ($this->isCsrfTokenValid('delete' . $plan->getId(), $request->request->get('_token'))) {
            $this->em->remove($plan);
            $this->em->flush();
            $this->addFlash('success', 'Plan eliminado correctamente.');
        } else {
            $this->addFlash('error', 'Token inválido.');
        }
        return $this->redirectToRoute('admin_planes_index');
    }
}