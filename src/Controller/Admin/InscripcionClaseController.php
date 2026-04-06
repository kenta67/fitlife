<?php

namespace App\Controller\Admin;

use App\Entity\InscripcionClase;
use App\Entity\Cliente;
use App\Entity\Clase;
use App\Repository\MembresiaClienteRepository;
use App\Form\InscripcionClaseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscripcionClaseController extends AbstractController
{
    #[Route('/admin/inscribir-clase/{claseId}/{clienteId}', name: 'admin_inscribir_clase')]
    public function inscribir(
        int $claseId,
        int $clienteId,
        EntityManagerInterface $em,
        MembresiaClienteRepository $membresiaClienteRepo,
        Request $request
    ): Response {
        $clase = $em->getRepository(Clase::class)->find($claseId);
        $cliente = $em->getRepository(Cliente::class)->find($clienteId);
        if (!$clase || !$cliente) {
            throw $this->createNotFoundException('Clase o cliente no encontrado.');
        }
        // 1. Validar estado del cliente
        if (!$cliente->isEstado()) {
            $this->addFlash('error', 'El cliente está inactivo y no puede inscribirse a clases.');
            return $this->redirectToRoute('admin_clase_show', ['id' => $claseId]);
        }
        // 2. Buscar membresía activa
        $hoy = new \DateTimeImmutable();
        $membresia = $membresiaClienteRepo->createQueryBuilder('mc')
            ->where('mc.cliente = :cliente')
            ->andWhere('mc.estado = true')
            ->andWhere('mc.fechaInicio <= :hoy')
            ->andWhere('mc.fechaVencimiento >= :hoy')
            ->setParameter('cliente', $cliente)
            ->setParameter('hoy', $hoy)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
        if (!$membresia) {
            $this->addFlash('error', 'El cliente no tiene una membresía activa.');
            return $this->redirectToRoute('admin_clase_show', ['id' => $claseId]);
        }
        // 3. Validar acceso a clases según el plan
        if (!$membresia->getPlan()->isIncluyeClases()) {
            $this->addFlash('error', 'El plan de membresía del cliente no permite inscribirse a clases.');
            return $this->redirectToRoute('admin_clase_show', ['id' => $claseId]);
        }
        // 4. Proceder con la inscripción
        $inscripcion = new InscripcionClase();
        $inscripcion->setCliente($cliente);
        $inscripcion->setClase($clase);
        $inscripcion->setFecha($hoy);
        $inscripcion->setEstado(true);
        $em->persist($inscripcion);
        $em->flush();
        $this->addFlash('success', 'Cliente inscrito correctamente en la clase.');
        return $this->redirectToRoute('admin_clase_show', ['id' => $claseId]);
    }
}
