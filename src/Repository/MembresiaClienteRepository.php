<?php

namespace App\Repository;

use App\Entity\MembresiaCliente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MembresiaCliente>
 */
class MembresiaClienteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MembresiaCliente::class);
    }

    /**
     * Cuenta las membresías vigentes (estado = true y fechaVencimiento >= hoy).
     */
    public function countVigentes(): int
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('mc')
            ->select('COUNT(mc.id)')
            ->where('mc.estado = :estado')
            ->andWhere('mc.fechaVencimiento >= :hoy')
            ->setParameter('estado', true)
            ->setParameter('hoy', $now->format('Y-m-d'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}