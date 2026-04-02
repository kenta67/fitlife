<?php

namespace App\Controller\Admin;

use App\Entity\Cliente;
use App\Form\ClienteType;
use App\Repository\ClienteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/clientes')]
#[IsGranted('ROLE_ADMIN')]
class ClienteController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/', name: 'admin_cliente_index', methods: ['GET'])]
    public function index(ClienteRepository $repo, Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $clientes = $repo->findPaginated($offset, $limit);
        $total = $repo->countAll();
        $totalPages = ceil($total / $limit);
        $stats = $repo->countByStatus();

        return $this->render('admin/clientes/index.html.twig', [
            'clientes' => $clientes,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalClientes' => $total,
            'stats' => $stats,
        ]);
    }

    #[Route('/new', name: 'admin_cliente_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $cliente = new Cliente();
        $cliente->setFechaRegistro(new \DateTimeImmutable());

        $form = $this->createForm(ClienteType::class, $cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($cliente);
            $this->em->flush();
            $this->addFlash('success', 'Cliente creado correctamente.');
            return $this->redirectToRoute('admin_cliente_index');
        }

        return $this->render('admin/clientes/new.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nuevo Cliente',
            'action' => 'Crear',
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_cliente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cliente $cliente): Response
    {
        $form = $this->createForm(ClienteType::class, $cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Cliente actualizado correctamente.');
            return $this->redirectToRoute('admin_cliente_index');
        }

        return $this->render('admin/clientes/edit.html.twig', [
            'form' => $form->createView(),
            'cliente' => $cliente,
            'title' => 'Editar Cliente',
            'action' => 'Actualizar',
        ]);
    }

    #[Route('/{id}/show', name: 'admin_cliente_show', methods: ['GET'])]
    public function show(Cliente $cliente): Response
    {
        return $this->render('admin/clientes/show.html.twig', [
            'cliente' => $cliente,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_cliente_delete', methods: ['POST'])]
    public function delete(Request $request, Cliente $cliente): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cliente->getId(), $request->request->get('_token'))) {
            $this->em->remove($cliente);
            $this->em->flush();
            $this->addFlash('success', 'Cliente eliminado correctamente.');
        } else {
            $this->addFlash('error', 'Token inválido.');
        }
        return $this->redirectToRoute('admin_cliente_index');
    }
}