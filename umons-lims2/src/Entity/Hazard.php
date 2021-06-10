<?php

namespace App\Entity;

use App\Repository\HazardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HazardRepository::class)
 */
class Hazard
{


    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, inversedBy="hazards")
     *
     */
    private $products;

    /**
     * @ORM\ManyToMany(targetEntity=Hazard::class)
     */
    private $incompatibilities;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->incompatibilities = new ArrayCollection();
    }


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getIncompatibilities(): Collection
    {
        return $this->incompatibilities;
    }

    public function addIncompatibility(self $incompatibility): self
    {
        if (!$this->incompatibilities->contains($incompatibility)) {
            $this->incompatibilities[] = $incompatibility;
            $incompatibility->addIncompatibility($this);
        }

        return $this;
    }

    public function removeIncompatibility(self $incompatibility): self
    {

        $this->incompatibilities->removeElement($incompatibility);
        $incompatibility->removeIncompatibility($this);

        return $this;
    }
}
