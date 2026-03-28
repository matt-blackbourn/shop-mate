<?php

namespace App\Entity;

use App\Enum\ListMemberRole;
use App\Repository\ListMemberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListMemberRepository::class)]
class ListMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'listMembers')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'listMembers')]
    private ?ShoppingList $shoppingList = null;

    #[ORM\Column(nullable: true, enumType: ListMemberRole::class)]
    private ?ListMemberRole $role = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getRole(): ?ListMemberRole
    {
        return $this->role;
    }

    public function setRole(?ListMemberRole $role): static
    {
        $this->role = $role;

        return $this;
    }
}
