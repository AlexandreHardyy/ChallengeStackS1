<?php

namespace App\Entity;

use App\Repository\OrderStateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderStateRepository::class)]
class OrderState
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $state = null;

    #[ORM\OneToMany(mappedBy: 'state', targetEntity: OrderHistory::class)]
    private Collection $orderHistories;

    public function __construct()
    {
        $this->orderHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, OrderHistory>
     */
    public function getOrderHistories(): Collection
    {
        return $this->orderHistories;
    }

    public function addOrderHistory(OrderHistory $orderHistory): self
    {
        if (!$this->orderHistories->contains($orderHistory)) {
            $this->orderHistories->add($orderHistory);
            $orderHistory->setState($this);
        }

        return $this;
    }

    public function removeOrderHistory(OrderHistory $orderHistory): self
    {
        if ($this->orderHistories->removeElement($orderHistory)) {
            // set the owning side to null (unless already changed)
            if ($orderHistory->getState() === $this) {
                $orderHistory->setState(null);
            }
        }

        return $this;
    }
}
