<?php

namespace App\Entity;

use App\Repository\AsistenciaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AsistenciaRepository::class)]
#[ORM\Table(name: 'asistencia')]
class Asistencia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Cliente::class)]
    #[ORM\JoinColumn(name: 'cliente_id', referencedColumnName: 'id', nullable: false)]
    private Cliente $cliente;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $fecha;

    #[ORM\Column(type: 'time')]
    private \DateTimeInterface $horaEntrada;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $horaSalida = null;

    public function getId(): ?int { return $this->id; }
    public function getCliente(): Cliente { return $this->cliente; }
    public function setCliente(Cliente $c): static { $this->cliente = $c; return $this; }
    public function getFecha(): \DateTimeInterface { return $this->fecha; }
    public function setFecha(\DateTimeInterface $f): static { $this->fecha = $f; return $this; }
    public function getHoraEntrada(): \DateTimeInterface { return $this->horaEntrada; }
    public function setHoraEntrada(\DateTimeInterface $h): static { $this->horaEntrada = $h; return $this; }
    public function getHoraSalida(): ?\DateTimeInterface { return $this->horaSalida; }
    public function setHoraSalida(?\DateTimeInterface $h): static { $this->horaSalida = $h; return $this; }
}