<?php

namespace App\Repository;

use App\Entity\Pago;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pago>
 */
class PagoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pago::class);
    }

    /**
     * Suma los ingresos del mes especificado (pagos confirmados, estado = true).
     */
    public function sumByMonth(string $year, string $month): float
    {
        $startDate = $year . '-' . $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate)); // Último día del mes

        $result = $this->createQueryBuilder('p')
            ->select('SUM(p.monto)')
            ->where('p.estado = :estado')
            ->andWhere('p.fechaPago >= :startDate')
            ->andWhere('p.fechaPago <= :endDate')
            ->setParameter('estado', true)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }
}