<?php

namespace App\Entity;

use App\Repository\PersonalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: PersonalRepository::class)]
#[ORM\Table(name: 'personal')]
class Personal implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Rol::class)]
    #[ORM\JoinColumn(name: 'id_rol', referencedColumnName: 'id', nullable: false)]
    private Rol $rol;

    #[ORM\Column(type: 'string', length: 100)]
    private string $nombre;

    #[ORM\Column(type: 'string', length: 80, unique: true)]
    private string $usuario;

    #[ORM\Column(type: 'string', length: 255)]
    private string $contrasena;

    #[ORM\Column(type: 'boolean')]
    private bool $estado = true;

    // ──────────────────────────────────────────
    // MÉTODOS REQUERIDOS POR UserInterface
    // ──────────────────────────────────────────

    /**
     * Identificador único del usuario (el campo usado para el login).
     * Symfony lo usará para buscar al usuario en la BD.
     */
    public function getUserIdentifier(): string
    {
        return $this->usuario;
    }

    /**
     * Devuelve los roles basados en el nombre del Rol en BD.
     * Mapeo: administrador → ROLE_ADMIN
     *         recepcionista → ROLE_RECEPCIONISTA
     *         instructor    → ROLE_INSTRUCTOR
     */
    public function getRoles(): array
    {
        $rolNombre = strtolower(trim($this->rol->getNombre()));

        $mapa = [
            'administrador' => 'ROLE_ADMIN',
            'recepcionista'  => 'ROLE_RECEPCIONISTA',
            'instructor'     => 'ROLE_INSTRUCTOR',
        ];

        $role = $mapa[$rolNombre] ?? 'ROLE_USER';

        // Symfony requiere que siempre exista al menos ROLE_USER
        return array_unique([$role, 'ROLE_USER']);
    }

    /**
     * Contraseña hasheada. Symfony la compara automáticamente.
     */
    public function getPassword(): string
    {
        return $this->contrasena;
    }

    /**
     * Elimina datos sensibles de memoria (tokens, contraseña en texto plano).
     * No es necesario con bcrypt, pero requerido por la interfaz.
     */
    public function eraseCredentials(): void
    {
        // No hay credenciales temporales que limpiar
    }

    // ──────────────────────────────────────────
    // GETTERS Y SETTERS NORMALES
    // ──────────────────────────────────────────

    public function getId(): ?int { return $this->id; }

    public function getRol(): Rol { return $this->rol; }
    public function setRol(Rol $rol): static { $this->rol = $rol; return $this; }

    public function getNombre(): string { return $this->nombre; }
    public function setNombre(string $nombre): static { $this->nombre = $nombre; return $this; }

    public function getUsuario(): string { return $this->usuario; }
    public function setUsuario(string $usuario): static { $this->usuario = $usuario; return $this; }

    /** Getter para la contraseña hasheada (alias legible) */
    public function getContrasena(): string { return $this->contrasena; }
    public function setContrasena(string $contrasena): static { $this->contrasena = $contrasena; return $this; }

    public function isEstado(): bool { return $this->estado; }
    public function setEstado(bool $estado): static { $this->estado = $estado; return $this; }
}