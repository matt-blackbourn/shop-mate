<?php

namespace App\Entity;

use App\Repository\ShoppingListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingListRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ShoppingList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateCreated = null;

    /**
     * @var Collection<int, ListItem>
     */
    // If a child entity is removed from the collection, Doctrine will DELETE it from the database when you flush.
    #[ORM\OneToMany(targetEntity: ListItem::class, mappedBy: 'shoppingList', cascade: ['persist'], orphanRemoval: true)]
    private Collection $listItems;

    #[ORM\Column]
    private ?bool $quickAddList = false;

    /**
     * @var Collection<int, ShoppingSession>
     */
    #[ORM\OneToMany(targetEntity: ShoppingSession::class, mappedBy: 'shoppingList')]
    private Collection $shoppingSessions;

    #[ORM\ManyToOne(inversedBy: 'shoppingLists')]
    private ?Household $household = null;

    
    public function __construct()
    {
        $this->listItems = new ArrayCollection();
        $this->shoppingSessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreated(): ?\DateTimeImmutable
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?\DateTimeImmutable $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }



    /**
     * @return Collection<int, ListItem>
     */
    public function getListItems(): Collection
    {
        return $this->listItems;
    }

    
    public function addListItem(ListItem $listItem): static
    {
        if (!$this->listItems->contains($listItem)) {
            $this->listItems->add($listItem);
            $listItem->setShoppingList($this);
        }

        return $this;
    }

    public function removeListItem(ListItem $listItem): static
    {
        if ($this->listItems->removeElement($listItem)) {
            // set the owning side to null (unless already changed)
            if ($listItem->getShoppingList() === $this) {
                $listItem->setShoppingList(null);
            }
        }

        return $this;
    }

    public function isQuickAddList(): ?bool
    {
        return $this->quickAddList;
    }

    public function setQuickAddList(bool $quickAddList): static
    {
        $this->quickAddList = $quickAddList;

        return $this;
    }

    /**
     * @return Collection<int, ShoppingSession>
     */
    public function getShoppingSessions(): Collection
    {
        return $this->shoppingSessions;
    }

    public function addShoppingSession(ShoppingSession $shoppingSession): static
    {
        if (!$this->shoppingSessions->contains($shoppingSession)) {
            $this->shoppingSessions->add($shoppingSession);
            $shoppingSession->setShoppingList($this);
        }

        return $this;
    }

    public function removeShoppingSession(ShoppingSession $shoppingSession): static
    {
        if ($this->shoppingSessions->removeElement($shoppingSession)) {
            // set the owning side to null (unless already changed)
            if ($shoppingSession->getShoppingList() === $this) {
                $shoppingSession->setShoppingList(null);
            }
        }

        return $this;
    }

    public function getHousehold(): ?Household
    {
        return $this->household;
    }

    public function setHousehold(?Household $household): static
    {
        $this->household = $household;

        return $this;
    }
}
