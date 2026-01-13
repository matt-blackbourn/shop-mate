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

    public function __construct()
    {
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
}
