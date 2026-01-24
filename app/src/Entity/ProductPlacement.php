<?php

namespace App\Entity;

use App\Repository\ProductPlacementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductPlacementRepository::class)]
class ProductPlacement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $aisleSide = null;

    #[ORM\ManyToOne(inversedBy: 'productPlacements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FoodItem $foodItem = null;

    #[ORM\ManyToOne(inversedBy: 'productPlacements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Supermarket $supermarket = null;

    #[ORM\ManyToOne(inversedBy: 'productPlacements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Edge $edge = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'productPlacements')]
    private ?self $supersededBy = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'supersededBy')]
    private Collection $productPlacements;

    #[ORM\ManyToOne(inversedBy: 'productPlacements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlacementType $type = null;

    #[ORM\ManyToOne(inversedBy: 'productPlacements')]
    private ?User $suggestedBy = null;

    public function __construct()
    {
        $this->productPlacements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAisleSide(): ?int
    {
        return $this->aisleSide;
    }

    public function setAisleSide(?int $aisleSide): static
    {
        $this->aisleSide = $aisleSide;

        return $this;
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

    public function getSupersededBy(): ?self
    {
        return $this->supersededBy;
    }

    public function setSupersededBy(?self $supersededBy): static
    {
        $this->supersededBy = $supersededBy;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getProductPlacements(): Collection
    {
        return $this->productPlacements;
    }

    public function addProductPlacement(self $productPlacement): static
    {
        if (!$this->productPlacements->contains($productPlacement)) {
            $this->productPlacements->add($productPlacement);
            $productPlacement->setSupersededBy($this);
        }

        return $this;
    }

    public function removeProductPlacement(self $productPlacement): static
    {
        if ($this->productPlacements->removeElement($productPlacement)) {
            // set the owning side to null (unless already changed)
            if ($productPlacement->getSupersededBy() === $this) {
                $productPlacement->setSupersededBy(null);
            }
        }

        return $this;
    }

    public function getType(): ?PlacementType
    {
        return $this->type;
    }

    public function setType(?PlacementType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSuggestedBy(): ?User
    {
        return $this->suggestedBy;
    }

    public function setSuggestedBy(?User $suggestedBy): static
    {
        $this->suggestedBy = $suggestedBy;

        return $this;
    }
}
