<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: "json")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, ProductPlacement>
     */
    #[ORM\OneToMany(targetEntity: ProductPlacement::class, mappedBy: 'suggestedBy')]
    private Collection $productPlacements;

    /**
     * @var Collection<int, ShoppingList>
     */
    #[ORM\OneToMany(targetEntity: ShoppingList::class, mappedBy: 'user')]
    private Collection $shoppingLists;

    /**
     * @var Collection<int, FoodItem>
     */
    #[ORM\OneToMany(targetEntity: FoodItem::class, mappedBy: 'user')]
    private Collection $foodItems;

    public function __construct()
    {
        $this->productPlacements = new ArrayCollection();
        $this->shoppingLists = new ArrayCollection();
        $this->foodItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
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
            $productPlacement->setSuggestedBy($this);
        }

        return $this;
    }

    public function removeProductPlacement(ProductPlacement $productPlacement): static
    {
        if ($this->productPlacements->removeElement($productPlacement)) {
            // set the owning side to null (unless already changed)
            if ($productPlacement->getSuggestedBy() === $this) {
                $productPlacement->setSuggestedBy(null);
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
            $shoppingList->setUser($this);
        }

        return $this;
    }

    public function removeShoppingList(ShoppingList $shoppingList): static
    {
        if ($this->shoppingLists->removeElement($shoppingList)) {
            // set the owning side to null (unless already changed)
            if ($shoppingList->getUser() === $this) {
                $shoppingList->setUser(null);
            }
        }

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
            $foodItem->setUser($this);
        }

        return $this;
    }

    public function removeFoodItem(FoodItem $foodItem): static
    {
        if ($this->foodItems->removeElement($foodItem)) {
            // set the owning side to null (unless already changed)
            if ($foodItem->getUser() === $this) {
                $foodItem->setUser(null);
            }
        }

        return $this;
    }
}
