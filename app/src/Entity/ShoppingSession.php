<?php

namespace App\Entity;

use App\Repository\ShoppingSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingSessionRepository::class)]
class ShoppingSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\ManyToOne(inversedBy: 'shoppingSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShoppingList $shoppingList = null;

    #[ORM\ManyToOne(inversedBy: 'shoppingSessions')]
    private ?Node $currentNode = null;

    /**
     * @var Collection<int, ListItem>
     */
    #[ORM\OneToMany(targetEntity: ListItem::class, mappedBy: 'session')]
    private Collection $listItems;

    #[ORM\ManyToOne(inversedBy: 'shoppingSessions')]
    private ?Supermarket $supermarket = null;

    public function __construct()
    {
        $this->listItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getShoppingList(): ?ShoppingList
    {
        return $this->shoppingList;
    }

    public function setShoppingList(?ShoppingList $shoppingList): static
    {
        $this->shoppingList = $shoppingList;

        return $this;
    }

    public function getCurrentNode(): ?Node
    {
        return $this->currentNode;
    }

    public function setCurrentNode(?Node $currentNode): static
    {
        $this->currentNode = $currentNode;

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
            $listItem->setSession($this);
        }

        return $this;
    }

    public function removeListItem(ListItem $listItem): static
    {
        if ($this->listItems->removeElement($listItem)) {
            // set the owning side to null (unless already changed)
            if ($listItem->getSession() === $this) {
                $listItem->setSession(null);
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
