<?php

namespace App\Entity;

use App\Enum\ListInviteStatus;
use App\Repository\ListInviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListInviteRepository::class)]
class ListInvite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateSent = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateAccepted = null;

    #[ORM\Column(enumType: ListInviteStatus::class)]
    private ?ListInviteStatus $status = null;

    #[ORM\ManyToOne(inversedBy: 'listInvites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShoppingList $shoppingList = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getDateSent(): ?\DateTimeImmutable
    {
        return $this->dateSent;
    }

    public function setDateSent(\DateTimeImmutable $dateSent): static
    {
        $this->dateSent = $dateSent;

        return $this;
    }

    public function getDateAccepted(): ?\DateTimeImmutable
    {
        return $this->dateAccepted;
    }

    public function setDateAccepted(\DateTimeImmutable $dateAccepted): static
    {
        $this->dateAccepted = $dateAccepted;

        return $this;
    }

    public function getStatus(): ?ListInviteStatus
    {
        return $this->status;
    }

    public function setStatus(ListInviteStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getShoppingList(): ?ShoppingList
    {
        return $this->shoppingList;
    }

    public function setShoppingList(?ShoppingList $shoppingList): static
    {
        $this->shoppingList = $shoppingList;

        return $this;
    }
}
