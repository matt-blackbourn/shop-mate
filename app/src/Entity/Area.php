<?php

namespace App\Entity;

use App\Repository\AreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AreaRepository::class)]
class Area
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $orderBy = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colour = null;

    /**
     * @var Collection<int, FoodItem>
     */
    #[ORM\OneToMany(targetEntity: FoodItem::class, mappedBy: 'area')]
    private Collection $foodItems;

    public function __construct()
    {
        $this->foodItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getOrderBy(): ?int
    {
        return $this->orderBy;
    }

    public function setOrderBy(?int $orderBy): static
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function getColour(): ?string
    {
        return $this->colour;
    }

    public function setColour(?string $colour): static
    {
        $this->colour = $colour;

        return $this;
    }

    /**
     * @return Collection<int, FoodItem>
     */
    public function getFoodItems(): Collection
    {
        return $this->foodItems;
    }

    public function addFoodItem(FoodItem $foodItem): static
    {
        if (!$this->foodItems->contains($foodItem)) {
            $this->foodItems->add($foodItem);
            $foodItem->setArea($this);
        }

        return $this;
    }

    public function removeFoodItem(FoodItem $foodItem): static
    {
        if ($this->foodItems->removeElement($foodItem)) {
            // set the owning side to null (unless already changed)
            if ($foodItem->getArea() === $this) {
                $foodItem->setArea(null);
            }
        }

        return $this;
    }
}
