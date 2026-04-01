<?php

namespace App\Repository;

use App\Entity\MembresiaPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MembresiaPlan>
 */
class MembresiaPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MembresiaPlan::class);
    }

    // ... otros métodos existentes ...

    /**
     * Obtiene una página de planes.
     */
    public function findPaginated(int $offset, int $limit): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.nombrePlan', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Cuenta el total de planes (sin filtros).
     */
    public function countAll(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Cuenta planes activos e inactivos.
     */
    public function countByStatus(): array
    {
        $total = $this->countAll();
        $activos = $this->count(['estado' => true]);

        return [
            'total' => $total,
            'activos' => $activos,
            'inactivos' => $total - $activos,
        ];
    }
}