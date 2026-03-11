<?php

namespace App\Entity;

use App\Repository\NodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NodeRepository::class)]
class Node
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $xValue = null;

    #[ORM\Column(nullable: true)]
    private ?int $yValue = null;

    /**
     * @var Collection<int, Edge>
     */
    #[ORM\OneToMany(targetEntity: Edge::class, mappedBy: 'start')]
    private Collection $edgeStart;

    /**
     * @var Collection<int, Edge>
     */
    #[ORM\OneToMany(targetEntity: Edge::class, mappedBy: 'end')]
    private Collection $edgeEnd;

    /**
     * @var Collection<int, Supermarket>
     */
    #[ORM\OneToMany(targetEntity: Supermarket::class, mappedBy: 'entranceNode')]
    private Collection $supermarkets;

    #[ORM\ManyToOne(inversedBy: 'nodes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Supermarket $supermarket = null;

    /**
     * @var Collection<int, ShoppingSession>
     */
    #[ORM\OneToMany(targetEntity: ShoppingSession::class, mappedBy: 'currentNode')]
    private Collection $shoppingSessions;

    public function __construct()
    {
        $this->edgeStart = new ArrayCollection();
        $this->edgeEnd = new ArrayCollection();
        $this->supermarkets = new ArrayCollection();
        $this->shoppingSessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getXValue(): ?int
    {
        return $this->xValue;
    }

    public function setXValue(?int $xValue): static
    {
        $this->xValue = $xValue;

        return $this;
    }

    public function getYValue(): ?int
    {
        return $this->yValue;
    }

    public function setYValue(?int $yValue): static
    {
        $this->yValue = $yValue;

        return $this;
    }

    /**
     * @return Collection<int, Edge>
     */
    public function getEdgeStart(): Collection
    {
        return $this->edgeStart;
    }

    public function addEdgeStart(Edge $edgeStart): static
    {
        if (!$this->edgeStart->contains($edgeStart)) {
            $this->edgeStart->add($edgeStart);
            $edgeStart->setStart($this);
        }

        return $this;
    }

    public function removeEdgeStart(Edge $edgeStart): static
    {
        if ($this->edgeStart->removeElement($edgeStart)) {
            // set the owning side to null (unless already changed)
            if ($edgeStart->getStart() === $this) {
                $edgeStart->setStart(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Edge>
     */
    public function getEdgeEnd(): Collection
    {
        return $this->edgeEnd;
    }

    public function addEdgeEnd(Edge $edgeEnd): static
    {
        if (!$this->edgeEnd->contains($edgeEnd)) {
            $this->edgeEnd->add($edgeEnd);
            $edgeEnd->setEnd($this);
        }

        return $this;
    }

    public function removeEdgeEnd(Edge $edgeEnd): static
    {
        if ($this->edgeEnd->removeElement($edgeEnd)) {
            // set the owning side to null (unless already changed)
            if ($edgeEnd->getEnd() === $this) {
                $edgeEnd->setEnd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Supermarket>
     */
    public function getSupermarkets(): Collection
    {
        return $this->supermarkets;
    }

    public function addSupermarket(Supermarket $supermarket): static
    {
        if (!$this->supermarkets->contains($supermarket)) {
            $this->supermarkets->add($supermarket);
            $supermarket->setEntranceNode($this);
        }

        return $this;
    }

    public function removeSupermarket(Supermarket $supermarket): static
    {
        if ($this->supermarkets->removeElement($supermarket)) {
            // set the owning side to null (unless already changed)
            if ($supermarket->getEntranceNode() === $this) {
                $supermarket->setEntranceNode(null);
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
            $shoppingSession->setCurrentNode($this);
        }

        return $this;
    }

    public function removeShoppingSession(ShoppingSession $shoppingSession): static
    {
        if ($this->shoppingSessions->removeElement($shoppingSession)) {
            // set the owning side to null (unless already changed)
            if ($shoppingSession->getCurrentNode() === $this) {
                $shoppingSession->setCurrentNode(null);
            }
        }

        return $this;
    }
}
