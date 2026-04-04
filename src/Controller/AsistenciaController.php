<?php

namespace App\Controller;

use App\Entity\Asistencia;
use App\Repository\ClienteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/asistencia')]
#[IsGranted('ROLE_ADMIN')] // NUEVO: Solo administradores y recepcionistas pueden usar el escáner
class AsistenciaController extends AbstractController
{
    #[Route('/', name: 'asistencia_scanner')]
    public function scanner(): Response
    {
        // NUEVO: Renderiza la vista del escáner QR
        return $this->render('asistencia/scanner.html.twig');
    }

    #[Route('/registrar', name: 'asistencia_registrar', methods: ['POST'])]
    public function registrar(Request $request, ClienteRepository $clienteRepo, EntityManagerInterface $em): JsonResponse
    {
        // NUEVO: Recibe el token enviado desde el escáner
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;

        if (!$token) {
            return $this->json(['error' => 'Token no proporcionado'], 400);
        }

        // Buscar cliente por el token
        $cliente = $clienteRepo->findOneByQrCodigo($token);
        if (!$cliente) {
            return $this->json(['error' => 'Cliente no válido'], 404);
        }

        $hoy = new \DateTime();
        $fechaHoy = $hoy->format('Y-m-d');
        $horaActual = new \DateTime();

        // Buscar si ya tiene una asistencia abierta (sin hora de salida) hoy
        $visitaAbierta = null;
        foreach ($cliente->getAsistencias() as $asistencia) {
            if ($asistencia->getFecha()->format('Y-m-d') === $fechaHoy && $asistencia->getHoraSalida() === null) {
                $visitaAbierta = $asistencia;
                break;
            }
        }

        if ($visitaAbierta === null) {
            // Registrar entrada
            $asistencia = new Asistencia();
            $asistencia->setCliente($cliente);
            $asistencia->setFecha($hoy);
            $asistencia->setHoraEntrada($horaActual);
            $em->persist($asistencia);
            $em->flush();

            return $this->json([
                'mensaje' => "Bienvenido {$cliente->getNombre()} {$cliente->getApellido()} — entrada registrada",
                'tipo' => 'entrada',
            ]);
        }

        // Registrar salida
        $visitaAbierta->setHoraSalida($horaActual);
        $em->flush();

        return $this->json([
            'mensaje' => "Salida registrada. Gracias, {$cliente->getNombre()}",
            'tipo' => 'salida',
        ]);
    }
}