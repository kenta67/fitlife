<?php

namespace App\Controller\Admin;

use App\Entity\Clase;
use App\Form\ClaseType;
use App\Repository\ClaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/clases')]
#[IsGranted('ROLE_ADMIN')]
class ClaseController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/', name: 'admin_clase_index', methods: ['GET'])]
    public function index(ClaseRepository $repo, Request $request): Response
    {
        // Paginación
        $page = (int) $request->query->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $clases = $repo->findPaginated($offset, $limit);
        $total = $repo->countAll();
        $totalPages = ceil($total / $limit);

        // Estadísticas
        $stats = $repo->countByStatus();
        $stats['cuposTotales'] = $repo->sumCapacidadMax();

        // Para cada clase, obtener cantidad de inscritos
        foreach ($clases as $clase) {
            $inscritos[$clase->getId()] = $repo->countInscripcionesActivas($clase->getId());
        }

        return $this->render('admin/clases/index.html.twig', [
            'clases' => $clases,
            'inscritos' => $inscritos ?? [],
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalClases' => $total,
            'stats' => $stats,
        ]);
    }

    #[Route('/new/modal', name: 'admin_clase_new_modal', methods: ['GET'])]
    public function newModal(): Response
    {
        $clase = new Clase();
        $form = $this->createForm(ClaseType::class, $clase, [
            'action' => $this->generateUrl('admin_clase_new'),
        ]);

        return $this->render('admin/clases/_form_modal.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nueva Clase',
            'action' => 'Crear',
        ]);
    }

    #[Route('/{id}/edit/modal', name: 'admin_clase_edit_modal', methods: ['GET'])]
    public function editModal(Clase $clase): Response
    {
        $form = $this->createForm(ClaseType::class, $clase, [
            'action' => $this->generateUrl('admin_clase_edit', ['id' => $clase->getId()]),
        ]);

        return $this->render('admin/clases/_form_modal.html.twig', [
            'form' => $form->createView(),
            'title' => 'Editar Clase',
            'action' => 'Actualizar',
        ]);
    }

    #[Route('/{id}/detalles/modal', name: 'admin_clase_detalles_modal', methods: ['GET'])]
    public function detallesModal(Clase $clase, ClaseRepository $repo): Response
    {
        $clientes = $repo->findClientesInscritos($clase->getId());
        return $this->render('admin/clases/_detalles_modal.html.twig', [
            'clase' => $clase,
            'clientes' => $clientes,
        ]);
    }

    #[Route('/new', name: 'admin_clase_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $clase = new Clase();
        $form = $this->createForm(ClaseType::class, $clase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($clase);
            $this->em->flush();
            $this->addFlash('success', 'Clase creada correctamente.');
        } else {
            $this->addFlash('error', 'Error al crear la clase. Verifique los datos.');
        }

        return $this->redirectToRoute('admin_clase_index');
    }

    #[Route('/{id}/edit', name: 'admin_clase_edit', methods: ['POST'])]
    public function edit(Request $request, Clase $clase): Response
    {
        $form = $this->createForm(ClaseType::class, $clase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Clase actualizada correctamente.');
        } else {
            $this->addFlash('error', 'Error al actualizar la clase. Verifique los datos.');
        }

        return $this->redirectToRoute('admin_clase_index');
    }

    #[Route('/{id}/delete', name: 'admin_clase_delete', methods: ['POST'])]
    public function delete(Request $request, Clase $clase): Response
    {
        if ($this->isCsrfTokenValid('delete' . $clase->getId(), $request->request->get('_token'))) {
            $this->em->remove($clase);
            $this->em->flush();
            $this->addFlash('success', 'Clase eliminada correctamente.');
        } else {
            $this->addFlash('error', 'Token inválido.');
        }

        return $this->redirectToRoute('admin_clase_index');
    }
}