<?php

namespace App\Entity;

use App\Enum\SupermarketType;
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

    /**
     * @var Collection<int, Edge>
     */
    #[ORM\OneToMany(targetEntity: Edge::class, mappedBy: 'supermarket')]
    private Collection $edges;
    
    /**
     * @var Collection<int, Node>
     */
    #[ORM\OneToMany(targetEntity: Node::class, mappedBy: 'supermarket')]
    private Collection $nodes;

    #[ORM\ManyToOne(inversedBy: 'supermarkets')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Node $entranceNode = null;
    
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
     * @var Collection<int, Shelf>
     */
    #[ORM\OneToMany(targetEntity: Shelf::class, mappedBy: 'supermarket')]
    private Collection $shelves;

    /**
     * @var Collection<int, ShoppingSession>
     */
    #[ORM\OneToMany(targetEntity: ShoppingSession::class, mappedBy: 'supermarket')]
    private Collection $shoppingSessions;

    #[ORM\ManyToOne(inversedBy: 'supermarkets')]
    private ?WalkingPath $walkingPath = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateCreated = null;

    #[ORM\Column(nullable: true, enumType: SupermarketType::class)]
    private ?SupermarketType $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $suburb = null;

    #[ORM\Column]
    private ?bool $active = false;

    #[ORM\Column(nullable: true)]
    private ?array $scaledPathData = null;

    #[ORM\Column(nullable: true)]
    private ?int $aisleWidth = null;

    #[ORM\Column(nullable: true)]
    private ?int $shelfDepth = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'defaultSupermarket')]
    private Collection $userDefaultSupermarkets;

    public function __construct()
    {
        $this->edges = new ArrayCollection();
        $this->nodes = new ArrayCollection();
        $this->productPlacements = new ArrayCollection();
        $this->shelves = new ArrayCollection();
        $this->shoppingSessions = new ArrayCollection();
        $this->userDefaultSupermarkets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): static
    {
        $this->id = $id;
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
     * @return Collection<int, Shelf>
     */
    public function getShelves(): Collection
    {
        return $this->shelves;
    }

    public function addShelf(Shelf $shelf): static
    {
        if (!$this->shelves->contains($shelf)) {
            $this->shelves->add($shelf);
            $shelf->setSupermarket($this);
        }

        return $this;
    }

    public function removeShelf(Shelf $shelf): static
    {
        if ($this->shelves->removeElement($shelf)) {
            // set the owning side to null (unless already changed)
            if ($shelf->getSupermarket() === $this) {
                $shelf->setSupermarket(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ShoppingSession>
     */
    public function getShoppingSessions(): Collection
    {
        return $this->shoppingSessions;
    }

    public function addShoppingSession(ShoppingSession $shoppingSession): static
    {
        if (!$this->shoppingSessions->contains($shoppingSession)) {
            $this->shoppingSessions->add($shoppingSession);
            $shoppingSession->setSupermarket($this);
        }

        return $this;
    }

    public function removeShoppingSession(ShoppingSession $shoppingSession): static
    {
        if ($this->shoppingSessions->removeElement($shoppingSession)) {
            // set the owning side to null (unless already changed)
            if ($shoppingSession->getSupermarket() === $this) {
                $shoppingSession->setSupermarket(null);
            }
        }

        return $this;
    }

    public function getWalkingPath(): ?WalkingPath
    {
        return $this->walkingPath;
    }

    public function setWalkingPath(?WalkingPath $walkingPath): static
    {
        $this->walkingPath = $walkingPath;

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

    public function getType(): ?SupermarketType
    {
        return $this->type;
    }

    public function setType(?SupermarketType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSuburb(): ?string
    {
        return $this->suburb;
    }

    public function setSuburb(?string $suburb): static
    {
        $this->suburb = $suburb;

        return $this;
    }


    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getScaledPathData(): ?array
    {
        return $this->scaledPathData;
    }

    public function setScaledPathData(?array $scaledPathData): static
    {
        $this->scaledPathData = $scaledPathData;

        return $this;
    }

    public function getAisleWidth(): ?int
    {
        return $this->aisleWidth;
    }

    public function setAisleWidth(?int $aisleWidth): static
    {
        $this->aisleWidth = $aisleWidth;

        return $this;
    }

    public function getShelfDepth(): ?int
    {
        return $this->shelfDepth;
    }

    public function setShelfDepth(?int $shelfDepth): static
    {
        $this->shelfDepth = $shelfDepth;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUserDefaultSupermarkets(): Collection
    {
        return $this->userDefaultSupermarkets;
    }

    public function addUserDefaultSupermarket(User $userDefaultSupermarket): static
    {
        if (!$this->userDefaultSupermarkets->contains($userDefaultSupermarket)) {
            $this->userDefaultSupermarkets->add($userDefaultSupermarket);
            $userDefaultSupermarket->setDefaultSupermarket($this);
        }

        return $this;
    }

    public function removeUserDefaultSupermarket(User $userDefaultSupermarket): static
    {
        if ($this->userDefaultSupermarkets->removeElement($userDefaultSupermarket)) {
            // set the owning side to null (unless already changed)
            if ($userDefaultSupermarket->getDefaultSupermarket() === $this) {
                $userDefaultSupermarket->setDefaultSupermarket(null);
            }
        }

        return $this;
    }
}
