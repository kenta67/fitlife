<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClienteRepository::class)]
#[ORM\Table(name: 'cliente')]
class Cliente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $nombre;

    #[ORM\Column(type: 'string', length: 100)]
    private string $apellido;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private string $cedula;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $correo = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $telefono = null;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $fechaRegistro;

    #[ORM\Column(type: 'boolean')]
    private bool $estado = true;

    public function getId(): ?int { return $this->id; }

    public function getNombre(): string { return $this->nombre; }
    public function setNombre(string $n): static { $this->nombre = $n; return $this; }

    public function getApellido(): string { return $this->apellido; }
    public function setApellido(string $a): static { $this->apellido = $a; return $this; }

    public function getCedula(): string { return $this->cedula; }
    public function setCedula(string $c): static { $this->cedula = $c; return $this; }

    public function getCorreo(): ?string { return $this->correo; }
    public function setCorreo(?string $correo): static { $this->correo = $correo; return $this; }

    public function getTelefono(): ?string { return $this->telefono; }
    public function setTelefono(?string $t): static { $this->telefono = $t; return $this; }

    public function getFechaRegistro(): \DateTimeInterface { return $this->fechaRegistro; }
    public function setFechaRegistro(\DateTimeInterface $f): static { $this->fechaRegistro = $f; return $this; }

    public function isEstado(): bool { return $this->estado; }
    public function setEstado(bool $e): static { $this->estado = $e; return $this; }
}