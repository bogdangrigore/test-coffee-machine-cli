<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductIngredient", mappedBy="productId", orphanRemoval=true)
     */
    private $productIngredients;

    public function __construct()
    {
        $this->productIngredients = new ArrayCollection();
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|ProductIngredient[]
     */
    public function getProductIngredients(): Collection
    {
        return $this->productIngredients;
    }

    public function addProductIngredient(ProductIngredient $productIngredient): self
    {
        if (!$this->productIngredients->contains($productIngredient)) {
            $this->productIngredients[] = $productIngredient;
            $productIngredient->setProductId($this);
        }

        return $this;
    }

    public function removeProductIngredient(ProductIngredient $productIngredient): self
    {
        if ($this->productIngredients->contains($productIngredient)) {
            $this->productIngredients->removeElement($productIngredient);
            // set the owning side to null (unless already changed)
            if ($productIngredient->getProductId() === $this) {
                $productIngredient->setProductId(null);
            }
        }

        return $this;
    }
}
