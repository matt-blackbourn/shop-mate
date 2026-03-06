<?php

namespace App\Entity;

use App\Enum\SupermarketType;
use App\Repository\FloorPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FloorPlanRepository::class)]
class FloorPlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $rawData = [];

    #[ORM\Column(length: 255)]
    private ?string $suburb = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreated = null;

    #[ORM\ManyToOne(inversedBy: 'floorPlans')]
    private ?User $user = null;

    #[ORM\Column(enumType: SupermarketType::class)]
    private ?SupermarketType $type = null;

    /**
     * @var Collection<int, Supermarket>
     */
    #[ORM\OneToMany(targetEntity: Supermarket::class, mappedBy: 'floorPlan')]
    private Collection $supermarkets;

    public function __construct()
    {
        $this->supermarkets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function setRawData(array $rawData): static
    {
        $this->rawData = $rawData;

        return $this;
    }

    public function getSuburb(): ?string
    {
        return $this->suburb;
    }

    public function setSuburb(string $suburb): static
    {
        $this->suburb = $suburb;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeImmutable
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeImmutable $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?SupermarketType
    {
        return $this->type;
    }

    public function setType(SupermarketType $type): static
    {
        $this->type = $type;

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
            $supermarket->setFloorPlan($this);
        }

        return $this;
    }

    public function removeSupermarket(Supermarket $supermarket): static
    {
        if ($this->supermarkets->removeElement($supermarket)) {
            // set the owning side to null (unless already changed)
            if ($supermarket->getFloorPlan() === $this) {
                $supermarket->setFloorPlan(null);
            }
        }

        return $this;
    }
}
