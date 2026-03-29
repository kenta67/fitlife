<?php

namespace App\Entity;

use App\Repository\ClaseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClaseRepository::class)]
#[ORM\Table(name: 'clase')]
class Clase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $nombre;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: 'integer')]
    private int $capacidadMax = 20;

    #[ORM\ManyToOne(targetEntity: Personal::class)]
    #[ORM\JoinColumn(name: 'instructor_id', referencedColumnName: 'id', nullable: false)]
    private Personal $instructor;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $horario = null;

    #[ORM\Column(type: 'boolean')]
    private bool $estado = true;

    public function getId(): ?int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function setNombre(string $n): static { $this->nombre = $n; return $this; }
    public function getDescripcion(): ?string { return $this->descripcion; }
    public function setDescripcion(?string $d): static { $this->descripcion = $d; return $this; }
    public function getCapacidadMax(): int { return $this->capacidadMax; }
    public function setCapacidadMax(int $c): static { $this->capacidadMax = $c; return $this; }
    public function getInstructor(): Personal { return $this->instructor; }
    public function setInstructor(Personal $i): static { $this->instructor = $i; return $this; }
    public function getHorario(): ?string { return $this->horario; }
    public function setHorario(?string $h): static { $this->horario = $h; return $this; }
    public function isEstado(): bool { return $this->estado; }
    public function setEstado(bool $e): static { $this->estado = $e; return $this; }
}