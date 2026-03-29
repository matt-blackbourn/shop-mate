<?php

namespace App\Entity;

use App\Repository\ShoppingListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingListRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ShoppingList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateCreated = null;

    /**
     * @var Collection<int, ListItem>
     */
    // If a child entity is removed from the collection, Doctrine will DELETE it from the database when you flush.
    #[ORM\OneToMany(targetEntity: ListItem::class, mappedBy: 'shoppingList', cascade: ['persist'], orphanRemoval: true)]
    private Collection $listItems;

    #[ORM\Column]
    private ?bool $quickAddList = false;

    /**
     * @var Collection<int, ShoppingSession>
     */
    #[ORM\OneToMany(targetEntity: ShoppingSession::class, mappedBy: 'shoppingList')]
    private Collection $shoppingSessions;

    #[ORM\ManyToOne(inversedBy: 'shoppingLists')]
    private ?User $owner = null;

    /**
     * @var Collection<int, ListMember>
     */
    #[ORM\OneToMany(targetEntity: ListMember::class, mappedBy: 'shoppingList')]
    private Collection $listMembers;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'defaultList')]
    private Collection $users;

    /**
     * @var Collection<int, ListInvite>
     */
    #[ORM\OneToMany(targetEntity: ListInvite::class, mappedBy: 'shoppingList')]
    private Collection $listInvites;

    
    public function __construct()
    {
        $this->listItems = new ArrayCollection();
        $this->shoppingSessions = new ArrayCollection();
        $this->listMembers = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->listInvites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $listItem->setShoppingList($this);
        }

        return $this;
    }

    public function removeListItem(ListItem $listItem): static
    {
        if ($this->listItems->removeElement($listItem)) {
            // set the owning side to null (unless already changed)
            if ($listItem->getShoppingList() === $this) {
                $listItem->setShoppingList(null);
            }
        }

        return $this;
    }

    public function isQuickAddList(): ?bool
    {
        return $this->quickAddList;
    }

    public function setQuickAddList(bool $quickAddList): static
    {
        $this->quickAddList = $quickAddList;

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
            $shoppingSession->setShoppingList($this);
        }

        return $this;
    }

    public function removeShoppingSession(ShoppingSession $shoppingSession): static
    {
        if ($this->shoppingSessions->removeElement($shoppingSession)) {
            // set the owning side to null (unless already changed)
            if ($shoppingSession->getShoppingList() === $this) {
                $shoppingSession->setShoppingList(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, ListMember>
     */
    public function getListMembers(): Collection
    {
        return $this->listMembers;
    }

    public function addListMember(ListMember $listMember): static
    {
        if (!$this->listMembers->contains($listMember)) {
            $this->listMembers->add($listMember);
            $listMember->setShoppingList($this);
        }

        return $this;
    }

    public function removeListMember(ListMember $listMember): static
    {
        if ($this->listMembers->removeElement($listMember)) {
            // set the owning side to null (unless already changed)
            if ($listMember->getShoppingList() === $this) {
                $listMember->setShoppingList(null);
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
            $user->setDefaultList($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getDefaultList() === $this) {
                $user->setDefaultList(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ListInvite>
     */
    public function getListInvites(): Collection
    {
        return $this->listInvites;
    }

    public function addListInvite(ListInvite $listInvite): static
    {
        if (!$this->listInvites->contains($listInvite)) {
            $this->listInvites->add($listInvite);
            $listInvite->setShoppingList($this);
        }

        return $this;
    }

    public function removeListInvite(ListInvite $listInvite): static
    {
        if ($this->listInvites->removeElement($listInvite)) {
            // set the owning side to null (unless already changed)
            if ($listInvite->getShoppingList() === $this) {
                $listInvite->setShoppingList(null);
            }
        }

        return $this;
    }
}
