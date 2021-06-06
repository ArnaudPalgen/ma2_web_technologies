<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
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
     * @ORM\Column(type="string", length=255)
     */
    private $ncas;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $size;

    /**
     * @ORM\Column(type="integer")
     */
    private $concentration;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="products")
     */
    private $location;


    /**
     * @ORM\OneToMany(targetEntity=Usage::class, mappedBy="product")
     */
    private $usages;

    /**
     * @ORM\ManyToMany(targetEntity=Hazard::class, mappedBy="products")
     */
    private $hazards;

    public function __construct()
    {
        $this->hazards = new ArrayCollection();
        $this->usages = new ArrayCollection();
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

    public function getNcas(): ?string
    {
        return $this->ncas;
    }

    public function setNcas(string $ncas): self
    {
        $this->ncas = $ncas;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getConcentration(): ?int
    {
        return $this->concentration;
    }

    public function setConcentration(int $concentration): self
    {
        $this->concentration = $concentration;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|Usage[]
     */
    public function getUsages(): Collection
    {
        return $this->usages;
    }

    public function addUsage(Usage $usage): self
    {
        if (!$this->usages->contains($usage)) {
            $this->usages[] = $usage;
            $usage->setProduct($this);
        }

        return $this;
    }

    public function removeUsage(Usage $usage): self
    {
        if ($this->usages->removeElement($usage)) {
            // set the owning side to null (unless already changed)
            if ($usage->getProduct() === $this) {
                $usage->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Hazard[]
     */
    public function getHazards(): Collection
    {
        return $this->hazards;
    }

    public function addHazard(Hazard $hazard): self
    {
        if (!$this->hazards->contains($hazard)) {
            $this->hazards[] = $hazard;
            $hazard->addProduct($this);
        }

        return $this;
    }

    public function removeHazard(Hazard $hazard): self
    {
        if ($this->hazards->removeElement($hazard)) {
            $hazard->removeProduct($this);
        }

        return $this;
    }


}
