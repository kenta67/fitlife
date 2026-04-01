<?php

namespace App\Controller\Admin;

use App\Entity\Personal;
use App\Form\PersonalType;
use App\Repository\PersonalRepository;
use App\Security\Sha256PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/personal')]
#[IsGranted('ROLE_ADMIN')]
class PersonalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private Sha256PasswordHasher $hasher
    ) {}

    #[Route('/', name: 'admin_personal_index', methods: ['GET'])]
    public function index(PersonalRepository $repo, Request $request): Response
    {
        // Paginación
        $page = (int) $request->query->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $personales = $repo->findPaginated($offset, $limit);
        $total = $repo->countAll();
        $totalPages = ceil($total / $limit);

        // Estadísticas
        $countsByRole = $repo->countByRole();

        return $this->render('admin/personal/index.html.twig', [
            'personales' => $personales,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPersonal' => $total,
            'countsByRole' => $countsByRole,
        ]);
    }

    #[Route('/new/modal', name: 'admin_personal_new_modal', methods: ['GET'])]
    public function newModal(): Response
    {
        $personal = new Personal();
        $form = $this->createForm(PersonalType::class, $personal, [
            'is_edit' => false,
            'action' => $this->generateUrl('admin_personal_new'),
        ]);

        return $this->render('admin/personal/_form_modal.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nuevo Empleado',
            'action' => 'Crear',
        ]);
    }

    #[Route('/{id}/edit/modal', name: 'admin_personal_edit_modal', methods: ['GET'])]
    public function editModal(Personal $personal): Response
    {
        $form = $this->createForm(PersonalType::class, $personal, [
            'is_edit' => true,
            'action' => $this->generateUrl('admin_personal_edit', ['id' => $personal->getId()]),
        ]);

        return $this->render('admin/personal/_form_modal.html.twig', [
            'form' => $form->createView(),
            'title' => 'Editar Empleado',
            'action' => 'Actualizar',
        ]);
    }

    #[Route('/new', name: 'admin_personal_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $personal = new Personal();
        $form = $this->createForm(PersonalType::class, $personal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('contrasena')->getData();
            if ($plainPassword) {
                $hashed = $this->hasher->hash($plainPassword);
                $personal->setContrasena($hashed);
            }
            $this->em->persist($personal);
            $this->em->flush();

            $this->addFlash('success', 'Personal creado correctamente.');
        } else {
            $this->addFlash('error', 'Error al crear el personal. Verifique los datos.');
        }

        return $this->redirectToRoute('admin_personal_index');
    }

    #[Route('/{id}/edit', name: 'admin_personal_edit', methods: ['POST'])]
    public function edit(Request $request, Personal $personal): Response
    {
        $form = $this->createForm(PersonalType::class, $personal, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('contrasena')->getData();
            if ($plainPassword) {
                $hashed = $this->hasher->hash($plainPassword);
                $personal->setContrasena($hashed);
            }
            $this->em->flush();
            $this->addFlash('success', 'Personal actualizado correctamente.');
        } else {
            $this->addFlash('error', 'Error al actualizar el personal. Verifique los datos.');
        }

        return $this->redirectToRoute('admin_personal_index');
    }

    #[Route('/{id}/delete', name: 'admin_personal_delete', methods: ['POST'])]
    public function delete(Request $request, Personal $personal): Response
    {
        if ($this->isCsrfTokenValid('delete'.$personal->getId(), $request->request->get('_token'))) {
            $this->em->remove($personal);
            $this->em->flush();
            $this->addFlash('success', 'Personal eliminado correctamente.');
        } else {
            $this->addFlash('error', 'Token inválido.');
        }
        return $this->redirectToRoute('admin_personal_index');
    }
}