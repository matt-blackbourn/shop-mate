<?php

namespace App\Entity;

use App\Repository\FoodItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodItemRepository::class)]
class FoodItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'foodItems')]
    private ?Edge $edge = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $side = null;

    /**
     * @var Collection<int, ListItem>
     */
    #[ORM\OneToMany(targetEntity: ListItem::class, mappedBy: 'foodItem')]
    private Collection $listItems;

    #[ORM\ManyToOne(inversedBy: 'foodItems')]
    private ?FoodCategory $category = null;

    /**
     * @var Collection<int, ProductPlacement>
     */
    #[ORM\OneToMany(targetEntity: ProductPlacement::class, mappedBy: 'foodItem')]
    private Collection $productPlacements;

    public function __construct()
    {
        $this->listItems = new ArrayCollection();
        $this->productPlacements = new ArrayCollection();
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

    public function getEdge(): ?Edge
    {
        return $this->edge;
    }

    public function setEdge(?Edge $edge): static
    {
        $this->edge = $edge;

        return $this;
    }

    public function getSide(): ?int
    {
        return $this->side;
    }

    public function setSide(?int $side): static
    {
        $this->side = $side;

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
            $listItem->setFoodItem($this);
        }

        return $this;
    }

    public function removeListItem(ListItem $listItem): static
    {
        if ($this->listItems->removeElement($listItem)) {
            // set the owning side to null (unless already changed)
            if ($listItem->getFoodItem() === $this) {
                $listItem->setFoodItem(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?FoodCategory
    {
        return $this->category;
    }

    public function setCategory(?FoodCategory $category): static
    {
        $this->category = $category;

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
            $productPlacement->setFoodItem($this);
        }

        return $this;
    }

    public function removeProductPlacement(ProductPlacement $productPlacement): static
    {
        if ($this->productPlacements->removeElement($productPlacement)) {
            // set the owning side to null (unless already changed)
            if ($productPlacement->getFoodItem() === $this) {
                $productPlacement->setFoodItem(null);
            }
        }

        return $this;
    }
}
