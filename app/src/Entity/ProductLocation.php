<?php

namespace App\Entity;

use App\Repository\ProductLocationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductLocationRepository::class)]
class ProductLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productLocations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FoodItem $foodItem = null;

    #[ORM\ManyToOne(inversedBy: 'productLocations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Supermarket $supermarket = null;

    #[ORM\ManyToOne(inversedBy: 'productLocations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Edge $edge = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $aislePosition = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFoodItem(): ?FoodItem
    {
        return $this->foodItem;
    }

    public function setFoodItem(?FoodItem $foodItem): static
    {
        $this->foodItem = $foodItem;

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

    public function getEdge(): ?Edge
    {
        return $this->edge;
    }

    public function setEdge(?Edge $edge): static
    {
        $this->edge = $edge;

        return $this;
    }

    public function getAislePosition(): ?int
    {
        return $this->aislePosition;
    }

    public function setAislePosition(?int $aislePosition): static
    {
        $this->aislePosition = $aislePosition;

        return $this;
    }
}
