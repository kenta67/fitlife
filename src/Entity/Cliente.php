<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ClienteRepository::class)]
#[ORM\Table(name: 'cliente')]
#[UniqueEntity(fields: ['cedula'], message: 'Esta cédula ya está registrada.')]
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

    #[ORM\Column(type: 'date_immutable')]
    private \DateTimeImmutable $fechaRegistro;

    #[ORM\Column(type: 'boolean')]
    private bool $estado = true;

    // NUEVO: Campo para almacenar el token único del QR
    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    private ?string $qr_codigo = null;

    // NUEVO: Relación OneToMany con las asistencias (entrada/salida)
    #[ORM\OneToMany(mappedBy: 'cliente', targetEntity: Asistencia::class, cascade: ['persist', 'remove'])]
    private Collection $asistencias;

    public function __construct()
    {
        $this->asistencias = new ArrayCollection();
    }

    // Getters y Setters existentes ...

    public function getId(): ?int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function setNombre(string $nombre): static { $this->nombre = $nombre; return $this; }
    public function getApellido(): string { return $this->apellido; }
    public function setApellido(string $apellido): static { $this->apellido = $apellido; return $this; }
    public function getCedula(): string { return $this->cedula; }
    public function setCedula(string $cedula): static { $this->cedula = $cedula; return $this; }
    public function getCorreo(): ?string { return $this->correo; }
    public function setCorreo(?string $correo): static { $this->correo = $correo; return $this; }
    public function getTelefono(): ?string { return $this->telefono; }
    public function setTelefono(?string $telefono): static { $this->telefono = $telefono; return $this; }
    public function getFechaRegistro(): \DateTimeImmutable { return $this->fechaRegistro; }
    public function setFechaRegistro(\DateTimeImmutable $fechaRegistro): static { $this->fechaRegistro = $fechaRegistro; return $this; }
    public function isEstado(): bool { return $this->estado; }
    public function setEstado(bool $estado): static { $this->estado = $estado; return $this; }

    // NUEVOS GETTERS Y SETTERS
    public function getQrCodigo(): ?string
    {
        return $this->qr_codigo;
    }

    public function setQrCodigo(string $qr_codigo): static
    {
        $this->qr_codigo = $qr_codigo;
        return $this;
    }

    public function getAsistencias(): Collection
    {
        return $this->asistencias;
    }
}