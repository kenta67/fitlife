<?php

namespace App\Repository;

use App\Entity\Clase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Clase>
 */
class ClaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clase::class);
    }

    // ... otros métodos existentes ...

    /**
     * Obtiene una página de clases.
     */
    public function findPaginated(int $offset, int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.instructor', 'i')
            ->addSelect('i')
            ->orderBy('c.nombre', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Cuenta el total de clases.
     */
    public function countAll(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Cuenta clases activas e inactivas.
     */
    public function countByStatus(): array
    {
        $total = $this->countAll();
        $activas = $this->count(['estado' => true]);

        return [
            'total' => $total,
            'activas' => $activas,
            'inactivas' => $total - $activas,
        ];
    }

    /**
     * Devuelve la suma de capacidades máximas de todas las clases.
     */
    public function sumCapacidadMax(): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('SUM(c.capacidadMax)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Obtiene el número de inscripciones activas para una clase.
     */
    public function countInscripcionesActivas(int $claseId): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(ic.id)')
            ->leftJoin('c.inscripciones', 'ic')
            ->where('c.id = :claseId')
            ->andWhere('ic.estado = true')
            ->setParameter('claseId', $claseId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Obtiene los clientes inscritos activamente en una clase.
     */
    public function findClientesInscritos(int $claseId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('cliente')
            ->from('App\Entity\Cliente', 'cliente')
            ->innerJoin('App\Entity\InscripcionClase', 'ic', 'WITH', 'ic.cliente = cliente.id')
            ->where('ic.clase = :claseId')
            ->andWhere('ic.estado = true')
            ->setParameter('claseId', $claseId)
            ->orderBy('cliente.apellido', 'ASC');

        return $qb->getQuery()->getResult();
    }
}