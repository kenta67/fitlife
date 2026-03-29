<?php

namespace App\Entity;

use App\Repository\InscripcionClaseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscripcionClaseRepository::class)]
#[ORM\Table(name: 'inscripcion_clase')]
class InscripcionClase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Cliente::class)]
    #[ORM\JoinColumn(name: 'cliente_id', referencedColumnName: 'id', nullable: false)]
    private Cliente $cliente;

    #[ORM\ManyToOne(targetEntity: Clase::class)]
    #[ORM\JoinColumn(name: 'clase_id', referencedColumnName: 'id', nullable: false)]
    private Clase $clase;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $fecha;

    #[ORM\Column(type: 'boolean')]
    private bool $estado = true;

    public function getId(): ?int { return $this->id; }
    public function getCliente(): Cliente { return $this->cliente; }
    public function setCliente(Cliente $c): static { $this->cliente = $c; return $this; }
    public function getClase(): Clase { return $this->clase; }
    public function setClase(Clase $c): static { $this->clase = $c; return $this; }
    public function getFecha(): \DateTimeInterface { return $this->fecha; }
    public function setFecha(\DateTimeInterface $f): static { $this->fecha = $f; return $this; }
    public function isEstado(): bool { return $this->estado; }
    public function setEstado(bool $e): static { $this->estado = $e; return $this; }
}