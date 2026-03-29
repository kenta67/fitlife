<?php

namespace App\Entity;

use App\Repository\MembresiaClienteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MembresiaClienteRepository::class)]
#[ORM\Table(name: 'membresia_cliente')]
class MembresiaCliente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Cliente::class)]
    #[ORM\JoinColumn(name: 'cliente_id', referencedColumnName: 'id', nullable: false)]
    private Cliente $cliente;

    #[ORM\ManyToOne(targetEntity: MembresiaPlan::class)]
    #[ORM\JoinColumn(name: 'plan_id', referencedColumnName: 'id', nullable: false)]
    private MembresiaPlan $plan;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $fechaInicio;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $fechaVencimiento;

    #[ORM\Column(type: 'boolean')]
    private bool $estado = true;

    public function getId(): ?int { return $this->id; }

    public function getCliente(): Cliente { return $this->cliente; }
    public function setCliente(Cliente $c): static { $this->cliente = $c; return $this; }

    public function getPlan(): MembresiaPlan { return $this->plan; }
    public function setPlan(MembresiaPlan $p): static { $this->plan = $p; return $this; }

    public function getFechaInicio(): \DateTimeInterface { return $this->fechaInicio; }
    public function setFechaInicio(\DateTimeInterface $f): static { $this->fechaInicio = $f; return $this; }

    public function getFechaVencimiento(): \DateTimeInterface { return $this->fechaVencimiento; }
    public function setFechaVencimiento(\DateTimeInterface $f): static { $this->fechaVencimiento = $f; return $this; }

    public function isEstado(): bool { return $this->estado; }
    public function setEstado(bool $e): static { $this->estado = $e; return $this; }
}