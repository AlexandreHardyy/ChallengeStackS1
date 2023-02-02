<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $totalPaid = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $orderBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPaid(): ?float
    {
        return $this->totalPaid;
    }

    public function setTotalPaid(float $totalPaid): self
    {
        $this->totalPaid = $totalPaid;

        return $this;
    }

    public function getOrderBy(): ?User
    {
        return $this->orderBy;
    }

    public function setOrderBy(?User $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }
}
