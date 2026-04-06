<?php

namespace App\Controller\Admin;

use App\Entity\Clase;
use App\Entity\Cliente;
use App\Entity\InscripcionClase;
use App\Repository\ClaseRepository;
use App\Repository\ClienteRepository;
use App\Repository\MembresiaClienteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrarClienteClaseController extends AbstractController
{
    #[Route('/admin/registrar-cliente-clase', name: 'admin_registrar_cliente_clase')]
    public function registrar(
        Request $request,
        EntityManagerInterface $em,
        ClaseRepository $claseRepo,
        ClienteRepository $clienteRepo,
        MembresiaClienteRepository $membresiaClienteRepo
    ): Response {
        $clases = $claseRepo->findBy(['estado' => true]);
        $clientes = $clienteRepo->findBy(['estado' => true]);
        $mensaje = null;
        if ($request->isMethod('POST')) {
            $claseId = $request->request->get('claseId');
            $clienteId = $request->request->get('clienteId');
            $clase = $claseRepo->find($claseId);
            $cliente = $clienteRepo->find($clienteId);
            if (!$clase || !$cliente) {
                $mensaje = ['type' => 'error', 'text' => 'Clase o cliente no encontrado.'];
            } else if (!$cliente->isEstado()) {
                $mensaje = ['type' => 'error', 'text' => 'El cliente está inactivo y no puede inscribirse a clases.'];
            } else {
                $hoy = new \DateTime();
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
                    $mensaje = ['type' => 'error', 'text' => 'El cliente no tiene una membresía activa.'];
                } else if (!$membresia->getPlan()->isIncluyeClases()) {
                    $mensaje = ['type' => 'error', 'text' => 'El plan de membresía del cliente no permite inscribirse a clases.'];
                } else {
                    // Validar que no esté ya inscrito
                    $yaInscrito = $em->getRepository(InscripcionClase::class)->findOneBy([
                        'cliente' => $cliente,
                        'clase' => $clase,
                        'estado' => true
                    ]);
                    if ($yaInscrito) {
                        $mensaje = ['type' => 'error', 'text' => 'El cliente ya está inscrito en esta clase.'];
                    } else {
                        $inscripcion = new InscripcionClase();
                        $inscripcion->setCliente($cliente);
                        $inscripcion->setClase($clase);
                        $inscripcion->setFecha($hoy);
                        $inscripcion->setEstado(true);
                        $em->persist($inscripcion);
                        $em->flush();
                        $mensaje = ['type' => 'success', 'text' => 'Cliente inscrito correctamente en la clase.'];
                    }
                }
            }
        }
        return $this->render('admin/clientes/registrar_cliente_clase.html.twig', [
            'clases' => $clases,
            'clientes' => $clientes,
            'mensaje' => $mensaje,
        ]);
    }
}
