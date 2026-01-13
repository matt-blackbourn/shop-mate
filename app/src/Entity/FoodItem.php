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
    private ?Area $area = null;

    #[ORM\ManyToOne(inversedBy: 'foodItems')]
    private ?Edge $edge = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $side = null;

    /**
     * @var Collection<int, ProductLocation>
     */
    #[ORM\OneToMany(targetEntity: ProductLocation::class, mappedBy: 'foodItem')]
    private Collection $productLocations;

    /**
     * @var Collection<int, ShoppingList>
     */
    #[ORM\ManyToMany(targetEntity: ShoppingList::class, mappedBy: 'item')]
    private Collection $shoppingLists;

    public function __construct()
    {
        $this->productLocations = new ArrayCollection();
        $this->shoppingLists = new ArrayCollection();
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

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): static
    {
        $this->area = $area;

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
            $productLocation->setFoodItem($this);
        }

        return $this;
    }

    public function removeProductLocation(ProductLocation $productLocation): static
    {
        if ($this->productLocations->removeElement($productLocation)) {
            // set the owning side to null (unless already changed)
            if ($productLocation->getFoodItem() === $this) {
                $productLocation->setFoodItem(null);
            }
        }

        return $this;
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
            $shoppingList->addItem($this);
        }

        return $this;
    }

    public function removeShoppingList(ShoppingList $shoppingList): static
    {
        if ($this->shoppingLists->removeElement($shoppingList)) {
            $shoppingList->removeItem($this);
        }

        return $this;
    }
}
