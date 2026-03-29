<?php

namespace App\Repository;

use App\Entity\Personal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Personal>
 */
class PersonalRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personal::class);
    }

    /**
     * Busca un usuario por su nombre de usuario (usado en el login).
     */
    public function findByUsuario(string $usuario): ?Personal
    {
        return $this->findOneBy(['usuario' => $usuario]);
    }

    /**
     * Busca un usuario activo por su nombre de usuario.
     */
    public function findActiveByUsuario(string $usuario): ?Personal
    {
        return $this->findOneBy(['usuario' => $usuario, 'estado' => true]);
    }

    /**
     * Actualiza la contraseña de un usuario.
     * Requerido por PasswordUpgraderInterface.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Personal) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setContrasena($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Obtiene todos los usuarios activos ordenados por nombre.
     */
    public function findAllActive(): array
    {
        return $this->findBy(['estado' => true], ['nombre' => 'ASC']);
    }

    /**
     * Obtiene usuarios por rol.
     */
    public function findByRol($rolId): array
    {
        return $this->findBy(['rol' => $rolId], ['nombre' => 'ASC']);
    }

    /**
     * Cuenta usuarios activos.
     */
    public function countActive(): int
    {
        return $this->count(['estado' => true]);
    }
}
