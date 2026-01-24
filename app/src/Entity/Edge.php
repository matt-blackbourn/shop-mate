<?php

namespace App\Entity;

use App\Repository\EdgeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EdgeRepository::class)]
class Edge
{
    public const ENTRANCE_PHASE = 1;
    public const POST_ENTRANCE_PHASE = 2;
    public const MAIN_PHASE = 3;
    public const END_PHASE = 4;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $length = null;

    #[ORM\ManyToOne(inversedBy: 'edgeStart')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Node $start = null;

    #[ORM\ManyToOne(inversedBy: 'edgeEnd')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Node $end = null;

    /**
     * @var Collection<int, FoodItem>
     */
    #[ORM\OneToMany(targetEntity: FoodItem::class, mappedBy: 'edge')]
    private Collection $foodItems;

    #[ORM\ManyToOne(inversedBy: 'edges')]
    private ?Supermarket $supermarket = null;

    #[ORM\Column]
    private ?int $phase = self::MAIN_PHASE;

    /**
     * @var Collection<int, ProductPlacement>
     */
    #[ORM\OneToMany(targetEntity: ProductPlacement::class, mappedBy: 'edge')]
    private Collection $productPlacements;

    public function __construct()
    {
        $this->foodItems = new ArrayCollection();
        $this->productPlacements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getStart(): ?Node
    {
        return $this->start;
    }

    public function setStart(?Node $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?Node
    {
        return $this->end;
    }

    public function setEnd(?Node $end): static
    {
        $this->end = $end;

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
            $foodItem->setEdge($this);
        }

        return $this;
    }

    public function removeFoodItem(FoodItem $foodItem): static
    {
        if ($this->foodItems->removeElement($foodItem)) {
            // set the owning side to null (unless already changed)
            if ($foodItem->getEdge() === $this) {
                $foodItem->setEdge(null);
            }
        }

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

    public function getPhase(): ?int
    {
        return $this->phase;
    }

    public function setPhase(int $phase): static
    {
        $this->phase = $phase;

        return $this;
    }

    /**
     * @return Collection<int, ProductPlacement>
     */
    public function getProductPlacements(): Collection
    {
        return $this->productPlacements;
    }

    public function addProductPlacement(ProductPlacement $productPlacement): static
    {
        if (!$this->productPlacements->contains($productPlacement)) {
            $this->productPlacements->add($productPlacement);
            $productPlacement->setEdge($this);
        }

        return $this;
    }

    public function removeProductPlacement(ProductPlacement $productPlacement): static
    {
        if ($this->productPlacements->removeElement($productPlacement)) {
            // set the owning side to null (unless already changed)
            if ($productPlacement->getEdge() === $this) {
                $productPlacement->setEdge(null);
            }
        }

        return $this;
    }
}
