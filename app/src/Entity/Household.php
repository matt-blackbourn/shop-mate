<?php

namespace App\Entity;

use App\Repository\HouseholdRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HouseholdRepository::class)]
class Household
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, ShoppingList>
     */
    #[ORM\OneToMany(targetEntity: ShoppingList::class, mappedBy: 'household')]
    private Collection $shoppingLists;

    /**
     * @var Collection<int, HouseholdMember>
     */
    #[ORM\OneToMany(targetEntity: HouseholdMember::class, mappedBy: 'household')]
    private Collection $householdMembers;

    /**
     * @var Collection<int, HouseholdInvite>
     */
    #[ORM\OneToMany(targetEntity: HouseholdInvite::class, mappedBy: 'household')]
    private Collection $householdInvites;

    #[ORM\ManyToOne(inversedBy: 'households')]
    private ?User $user = null;

    public function __construct()
    {
        $this->shoppingLists = new ArrayCollection();
        $this->householdMembers = new ArrayCollection();
        $this->householdInvites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, ShoppingList>
     */
    public function getShoppingLists(): Collection
    {
        return $this->shoppingLists;
    }

    public function addShoppingList(ShoppingList $shoppingList): static
    {
        if (!$this->shoppingLists->contains($shoppingList)) {
            $this->shoppingLists->add($shoppingList);
            $shoppingList->setHousehold($this);
        }

        return $this;
    }

    public function removeShoppingList(ShoppingList $shoppingList): static
    {
        if ($this->shoppingLists->removeElement($shoppingList)) {
            // set the owning side to null (unless already changed)
            if ($shoppingList->getHousehold() === $this) {
                $shoppingList->setHousehold(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HouseholdMember>
     */
    public function getHouseholdMembers(): Collection
    {
        return $this->householdMembers;
    }

    public function addHouseholdMember(HouseholdMember $householdMember): static
    {
        if (!$this->householdMembers->contains($householdMember)) {
            $this->householdMembers->add($householdMember);
            $householdMember->setHousehold($this);
        }

        return $this;
    }

    public function removeHouseholdMember(HouseholdMember $householdMember): static
    {
        if ($this->householdMembers->removeElement($householdMember)) {
            // set the owning side to null (unless already changed)
            if ($householdMember->getHousehold() === $this) {
                $householdMember->setHousehold(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HouseholdInvite>
     */
    public function getHouseholdInvites(): Collection
    {
        return $this->householdInvites;
    }

    public function addHouseholdInvite(HouseholdInvite $householdInvite): static
    {
        if (!$this->householdInvites->contains($householdInvite)) {
            $this->householdInvites->add($householdInvite);
            $householdInvite->setHousehold($this);
        }

        return $this;
    }

    public function removeHouseholdInvite(HouseholdInvite $householdInvite): static
    {
        if ($this->householdInvites->removeElement($householdInvite)) {
            // set the owning side to null (unless already changed)
            if ($householdInvite->getHousehold() === $this) {
                $householdInvite->setHousehold(null);
            }
        }

        return $this;
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
}
