<?php

namespace App\Repository;

use App\Entity\Cliente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cliente>
 */
class ClienteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cliente::class);
    }

    /**
     * Cuenta los clientes activos (estado = true).
     */
    public function countActive(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.estado = :estado')
            ->setParameter('estado', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Obtiene una página de clientes.
     */
    public function findPaginated(int $offset, int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.apellido', 'ASC')
            ->addOrderBy('c.nombre', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Cuenta el total de clientes.
     */
    public function countAll(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Cuenta clientes activos e inactivos.
     */
    public function countByStatus(): array
    {
        $total = $this->countAll();
        $activos = $this->count(['estado' => true]);
        $inactivos = $total - $activos;

        // Nuevos registrados este mes (opcional)
        $now = new \DateTime();
        $firstDay = (new \DateTime())->setDate($now->format('Y'), $now->format('m'), 1);
        $nuevosEsteMes = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.fechaRegistro >= :firstDay')
            ->setParameter('firstDay', $firstDay)
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total' => $total,
            'activos' => $activos,
            'inactivos' => $inactivos,
            'nuevosEsteMes' => (int) $nuevosEsteMes,
        ];
    }
}