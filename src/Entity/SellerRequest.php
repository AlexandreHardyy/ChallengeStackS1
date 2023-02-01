<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\SellerRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SellerRequestRepository::class)]
class SellerRequest
{
    use TimestampableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'sellerRequest', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userRequest = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserRequest(): ?User
    {
        return $this->userRequest;
    }

    public function setUserRequest(User $userRequest): self
    {
        $this->userRequest = $userRequest;

        return $this;
    }
}
