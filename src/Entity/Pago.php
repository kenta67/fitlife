<?php

namespace App\Entity;

use App\Repository\PagoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PagoRepository::class)]
#[ORM\Table(name: 'pago')]
class Pago
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: MembresiaCliente::class)]
    #[ORM\JoinColumn(name: 'membresia_cliente_id', referencedColumnName: 'id', nullable: false)]
    private MembresiaCliente $membresiaCliente;

    #[ORM\ManyToOne(targetEntity: Personal::class)]
    #[ORM\JoinColumn(name: 'personal_id', referencedColumnName: 'id', nullable: false)]
    private Personal $personal;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $monto;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $fechaPago;

    #[ORM\Column(type: 'string', length: 50)]
    private string $metodoPago;

    #[ORM\Column(type: 'boolean')]
    private bool $estado = true;

    public function getId(): ?int { return $this->id; }

    public function getMembresiaCliente(): MembresiaCliente { return $this->membresiaCliente; }
    public function setMembresiaCliente(MembresiaCliente $m): static { $this->membresiaCliente = $m; return $this; }

    public function getPersonal(): Personal { return $this->personal; }
    public function setPersonal(Personal $p): static { $this->personal = $p; return $this; }

    public function getMonto(): string { return $this->monto; }
    public function setMonto(string $m): static { $this->monto = $m; return $this; }

    public function getFechaPago(): \DateTimeInterface { return $this->fechaPago; }
    public function setFechaPago(\DateTimeInterface $f): static { $this->fechaPago = $f; return $this; }

    public function getMetodoPago(): string { return $this->metodoPago; }
    public function setMetodoPago(string $m): static { $this->metodoPago = $m; return $this; }

    public function isEstado(): bool { return $this->estado; }
    public function setEstado(bool $e): static { $this->estado = $e; return $this; }
}