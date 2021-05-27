<?php

namespace App\Entity;

use App\Repository\ChemicalSafetyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChemicalSafetyRepository::class)
 */
class ChemicalSafety
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, mappedBy="chemical_safeties")
     */
    private $products;

    /**
     * @ORM\ManyToMany(targetEntity=ChemicalSafety::class, inversedBy="incompatibilities")
     */
    private $icompatibilities;

    /**
     * @ORM\ManyToMany(targetEntity=ChemicalSafety::class, mappedBy="icompatibilities")
     */
    private $incompatibilities;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->icompatibilities = new ArrayCollection();
        $this->incompatibilities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $product->addChemicalSafety($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            $product->removeChemicalSafety($this);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getIcompatibilities(): Collection
    {
        return $this->icompatibilities;
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
            $incompatibility->addIcompatibility($this);
        }

        return $this;
    }

    public function addIcompatibility(self $icompatibility): self
    {
        if (!$this->icompatibilities->contains($icompatibility)) {
            $this->icompatibilities[] = $icompatibility;
        }

        return $this;
    }

    public function removeIncompatibility(self $incompatibility): self
    {
        if ($this->incompatibilities->removeElement($incompatibility)) {
            $incompatibility->removeIcompatibility($this);
        }

        return $this;
    }

    public function removeIcompatibility(self $icompatibility): self
    {
        $this->icompatibilities->removeElement($icompatibility);

        return $this;
    }
}
