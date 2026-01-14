<?php

namespace App\Entity;

use App\Repository\ShoppingListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingListRepository::class)]
class ShoppingList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, FoodItem>
     */
    #[ORM\ManyToMany(targetEntity: FoodItem::class, inversedBy: 'shoppingLists', indexBy: 'id')]
    private Collection $item;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateCreated = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateCompleted = null;

    #[ORM\ManyToOne(inversedBy: 'shoppingLists')]
    private ?Supermarket $supermarket = null;

    public function __construct()
    {
        $this->item = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, FoodItem>
     */
    public function getItems(): Collection
    {
        return $this->item;
    }

    public function addItem(FoodItem $item): static
    {
        if (!$this->item->contains($item)) {
            $this->item->add($item);
        }

        return $this;
    }

    public function removeItem(FoodItem $item): static
    {
        $this->item->removeElement($item);

        return $this;
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

    public function getDateCompleted(): ?\DateTimeImmutable
    {
        return $this->dateCompleted;
    }

    public function setDateCompleted(?\DateTimeImmutable $dateCompleted): static
    {
        $this->dateCompleted = $dateCompleted;

        return $this;
    }

    public function getSupermarket(): ?Supermarket
    {
        return $this->supermarket;
    }

    public function setSupermarket(?Supermarket $supermarket): static
    {
        $this->supermarket = $supermarket;

        return $this;
    }
}
