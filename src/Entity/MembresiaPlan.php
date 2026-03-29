<?php

namespace App\Entity;

use App\Repository\MembresiaPlanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MembresiaPlanRepository::class)]
#[ORM\Table(name: 'membresia_plan')]
class MembresiaPlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $nombrePlan;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $costo;

    #[ORM\Column(type: 'integer')]
    private int $duracionDias;

    #[ORM\Column(type: 'boolean')]
    private bool $estado = true;

    public function getId(): ?int { return $this->id; }

    public function getNombrePlan(): string { return $this->nombrePlan; }
    public function setNombrePlan(string $n): static { $this->nombrePlan = $n; return $this; }

    public function getCosto(): string { return $this->costo; }
    public function setCosto(string $costo): static { $this->costo = $costo; return $this; }

    public function getDuracionDias(): int { return $this->duracionDias; }
    public function setDuracionDias(int $d): static { $this->duracionDias = $d; return $this; }

    public function isEstado(): bool { return $this->estado; }
    public function setEstado(bool $e): static { $this->estado = $e; return $this; }
}