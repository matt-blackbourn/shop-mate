<?php

namespace App\Entity;

use App\Repository\PlacementTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlacementTypeRepository::class)]
class PlacementType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, ProductPlacement>
     */
    #[ORM\OneToMany(targetEntity: ProductPlacement::class, mappedBy: 'type')]
    private Collection $productPlacements;

    public function __construct()
    {
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
            $productPlacement->setType($this);
        }

        return $this;
    }

    public function removeProductPlacement(ProductPlacement $productPlacement): static
    {
        if ($this->productPlacements->removeElement($productPlacement)) {
            // set the owning side to null (unless already changed)
            if ($productPlacement->getType() === $this) {
                $productPlacement->setType(null);
            }
        }

        return $this;
    }
}
