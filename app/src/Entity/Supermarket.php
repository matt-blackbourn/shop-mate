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
     * @var Collection<int, Edge>
     */
    #[ORM\OneToMany(targetEntity: Edge::class, mappedBy: 'supermarket')]
    private Collection $edges;

    #[ORM\ManyToOne(inversedBy: 'supermarkets')]
    private ?Node $entranceNode = null;

    /**
     * @var Collection<int, Node>
     */
    #[ORM\OneToMany(targetEntity: Node::class, mappedBy: 'supermarket')]
    private Collection $nodes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePath = null;

    #[ORM\Column(nullable: true)]
    private ?int $height = null;

    #[ORM\Column(nullable: true)]
    private ?int $width = null;

    /**
     * @var Collection<int, ProductPlacement>
     */
    #[ORM\OneToMany(targetEntity: ProductPlacement::class, mappedBy: 'supermarket')]
    private Collection $productPlacements;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'lastUsedSupermarket')]
    private Collection $users;

    public function __construct()
    {
        $this->edges = new ArrayCollection();
        $this->nodes = new ArrayCollection();
        $this->productPlacements = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getEntranceNode(): ?Node
    {
        return $this->entranceNode;
    }

    public function setEntranceNode(?Node $entranceNode): static
    {
        $this->entranceNode = $entranceNode;

        return $this;
    }

    /**
     * @return Collection<int, Node>
     */
    public function getNodes(): Collection
    {
        return $this->nodes;
    }

    public function addNode(Node $node): static
    {
        if (!$this->nodes->contains($node)) {
            $this->nodes->add($node);
            $node->setSupermarket($this);
        }

        return $this;
    }

    public function removeNode(Node $node): static
    {
        if ($this->nodes->removeElement($node)) {
            // set the owning side to null (unless already changed)
            if ($node->getSupermarket() === $this) {
                $node->setSupermarket(null);
            }
        }

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): static
    {
        $this->width = $width;

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
            $productPlacement->setSupermarket($this);
        }

        return $this;
    }

    public function removeProductPlacement(ProductPlacement $productPlacement): static
    {
        if ($this->productPlacements->removeElement($productPlacement)) {
            // set the owning side to null (unless already changed)
            if ($productPlacement->getSupermarket() === $this) {
                $productPlacement->setSupermarket(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setLastUsedSupermarket($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getLastUsedSupermarket() === $this) {
                $user->setLastUsedSupermarket(null);
            }
        }

        return $this;
    }
}
