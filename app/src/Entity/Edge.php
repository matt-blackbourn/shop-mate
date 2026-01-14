<?php

namespace App\Entity;

use App\Repository\EdgeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EdgeRepository::class)]
class Edge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

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

    /**
     * @var Collection<int, ProductLocation>
     */
    #[ORM\OneToMany(targetEntity: ProductLocation::class, mappedBy: 'edge')]
    private Collection $productLocations;

    #[ORM\ManyToOne(inversedBy: 'edges')]
    private ?Supermarket $supermarket = null;

    public function __construct()
    {
        $this->foodItems = new ArrayCollection();
        $this->productLocations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
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

    /**
     * @return Collection<int, ProductLocation>
     */
    public function getProductLocations(): Collection
    {
        return $this->productLocations;
    }

    public function addProductLocation(ProductLocation $productLocation): static
    {
        if (!$this->productLocations->contains($productLocation)) {
            $this->productLocations->add($productLocation);
            $productLocation->setEdge($this);
        }

        return $this;
    }

    public function removeProductLocation(ProductLocation $productLocation): static
    {
        if ($this->productLocations->removeElement($productLocation)) {
            // set the owning side to null (unless already changed)
            if ($productLocation->getEdge() === $this) {
                $productLocation->setEdge(null);
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
}
