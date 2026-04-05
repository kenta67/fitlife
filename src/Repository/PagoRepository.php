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







    public function findWithFilters(
        int $offset, 
        int $limit, 
        ?string $fechaDesde = null, 
        ?string $fechaHasta = null,
        ?int $idBusqueda = null
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.membresiaCliente', 'mc')
            ->leftJoin('mc.cliente', 'c')
            ->leftJoin('p.personal', 'per')
            ->addSelect('mc', 'c', 'per')
            ->orderBy('p.fechaPago', 'DESC');

        if ($fechaDesde) {
            $qb->andWhere('p.fechaPago >= :desde')
               ->setParameter('desde', $fechaDesde);
        }
        if ($fechaHasta) {
            $qb->andWhere('p.fechaPago <= :hasta')
               ->setParameter('hasta', $fechaHasta);
        }
        if ($idBusqueda) {
            $qb->andWhere('p.id = :id OR c.id = :id OR mc.id = :id')
               ->setParameter('id', $idBusqueda);
        }

        return $qb->setFirstResult($offset)
                  ->setMaxResults($limit)
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Cuenta total con filtros.
     */
    public function countWithFilters(
        ?string $fechaDesde = null, 
        ?string $fechaHasta = null,
        ?int $idBusqueda = null
    ): int {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->leftJoin('p.membresiaCliente', 'mc')
            ->leftJoin('mc.cliente', 'c');

        if ($fechaDesde) {
            $qb->andWhere('p.fechaPago >= :desde')
               ->setParameter('desde', $fechaDesde);
        }
        if ($fechaHasta) {
            $qb->andWhere('p.fechaPago <= :hasta')
               ->setParameter('hasta', $fechaHasta);
        }
        if ($idBusqueda) {
            $qb->andWhere('p.id = :id OR c.id = :id OR mc.id = :id')
               ->setParameter('id', $idBusqueda);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Suma de ingresos por rango de fechas.
     */
    public function sumByDateRange(?string $fechaDesde = null, ?string $fechaHasta = null): float
    {
        $qb = $this->createQueryBuilder('p')
            ->select('SUM(p.monto)')
            ->where('p.estado = true');

        if ($fechaDesde) {
            $qb->andWhere('p.fechaPago >= :desde')
               ->setParameter('desde', $fechaDesde);
        }
        if ($fechaHasta) {
            $qb->andWhere('p.fechaPago <= :hasta')
               ->setParameter('hasta', $fechaHasta);
        }

        $result = $qb->getQuery()->getSingleScalarResult();
        return $result ? (float) $result : 0.0;
    }

    /**
     * Estadísticas de pagos por método de pago.
     */
    public function sumByMetodoPago(?string $fechaDesde = null, ?string $fechaHasta = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.metodoPago as metodo, SUM(p.monto) as total, COUNT(p.id) as cantidad')
            ->where('p.estado = true')
            ->groupBy('p.metodoPago');

        if ($fechaDesde) {
            $qb->andWhere('p.fechaPago >= :desde')
               ->setParameter('desde', $fechaDesde);
        }
        if ($fechaHasta) {
            $qb->andWhere('p.fechaPago <= :hasta')
               ->setParameter('hasta', $fechaHasta);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Ingresos diarios para gráfica (últimos N días).
     */
    public function dailyIncome(int $dias = 30): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        // Usamos CONCATenación segura porque :dias no funciona dentro de DATE_SUB() con Doctrine
        $sql = "
            SELECT DATE(fecha_pago) as fecha, SUM(monto) as total 
            FROM pago 
            WHERE estado = 1 
            AND fecha_pago >= DATE_SUB(CURDATE(), INTERVAL " . (int)$dias . " DAY)
            GROUP BY DATE(fecha_pago)
            ORDER BY fecha ASC
        ";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        return $result->fetchAllAssociative();
    }

    /**
     * Todos los pagos para exportación (con filtros).
     */
    public function findAllForExport(
        ?string $fechaDesde = null, 
        ?string $fechaHasta = null
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.membresiaCliente', 'mc')
            ->leftJoin('mc.cliente', 'c')
            ->leftJoin('mc.plan', 'plan')
            ->leftJoin('p.personal', 'per')
            ->addSelect('mc', 'c', 'plan', 'per')
            ->where('p.estado = true')
            ->orderBy('p.fechaPago', 'DESC');

        if ($fechaDesde) {
            $qb->andWhere('p.fechaPago >= :desde')
               ->setParameter('desde', $fechaDesde);
        }
        if ($fechaHasta) {
            $qb->andWhere('p.fechaPago <= :hasta')
               ->setParameter('hasta', $fechaHasta);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Cuenta total general.
     */
    public function countAll(): int
    {
        return $this->count(['estado' => true]);
    }
}