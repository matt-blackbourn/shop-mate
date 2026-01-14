<?php

namespace App\Entity;

use App\Repository\SupermarketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupermarketRepository::class)]
class Supermarket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, ProductLocation>
     */
    #[ORM\OneToMany(targetEntity: ProductLocation::class, mappedBy: 'supermarket')]
    private Collection $productLocations;

    /**
     * @var Collection<int, Edge>
     */
    #[ORM\OneToMany(targetEntity: Edge::class, mappedBy: 'supermarket')]
    private Collection $edges;

    /**
     * @var Collection<int, ShoppingList>
     */
    #[ORM\OneToMany(targetEntity: ShoppingList::class, mappedBy: 'supermarket')]
    private Collection $shoppingLists;

    #[ORM\ManyToOne(inversedBy: 'supermarkets')]
    private ?Node $entranceNode = null;

    public function __construct()
    {
        $this->productLocations = new ArrayCollection();
        $this->edges = new ArrayCollection();
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
            $productLocation->setSupermarket($this);
        }

        return $this;
    }

    public function removeProductLocation(ProductLocation $productLocation): static
    {
        if ($this->productLocations->removeElement($productLocation)) {
            // set the owning side to null (unless already changed)
            if ($productLocation->getSupermarket() === $this) {
                $productLocation->setSupermarket(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Edge>
     */
    public function getEdges(): Collection
    {
        return $this->edges;
    }

    public function addEdge(Edge $edge): static
    {
        if (!$this->edges->contains($edge)) {
            $this->edges->add($edge);
            $edge->setSupermarket($this);
        }

        return $this;
    }

    public function removeEdge(Edge $edge): static
    {
        if ($this->edges->removeElement($edge)) {
            // set the owning side to null (unless already changed)
            if ($edge->getSupermarket() === $this) {
                $edge->setSupermarket(null);
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
            $shoppingList->setSupermarket($this);
        }

        return $this;
    }

    public function removeShoppingList(ShoppingList $shoppingList): static
    {
        if ($this->shoppingLists->removeElement($shoppingList)) {
            // set the owning side to null (unless already changed)
            if ($shoppingList->getSupermarket() === $this) {
                $shoppingList->setSupermarket(null);
            }
        }

        return $this;
    }

    public function getEntranceNode(): ?Node
    {
        return $this->entranceNode;
    }

    public function setEntranceNode(?Node $entranceNode): static
    {
        $this->entranceNode = $entranceNode;

        return $this;
    }
}
