<?php

namespace App\Controller\Admin;

use App\Entity\MembresiaCliente;
use App\Form\MembresiaClienteType;
use App\Repository\MembresiaClienteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MembresiaClienteController extends AbstractController
{
    #[Route('/admin/membresia-cliente/nuevo', name: 'admin_membresia_cliente_nuevo')]
    public function nuevo(Request $request, EntityManagerInterface $em): Response
    {
        $membresiaCliente = new MembresiaCliente();
        $form = $this->createForm(MembresiaClienteType::class, $membresiaCliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($membresiaCliente);
            $em->flush();
            $this->addFlash('success', 'Membresía asignada correctamente.');
            return $this->redirectToRoute('admin_membresia_cliente_nuevo');
        }

        return $this->render('admin/membresia_cliente/nuevo.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
