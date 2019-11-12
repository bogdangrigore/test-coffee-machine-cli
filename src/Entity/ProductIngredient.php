<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductIngredientRepository")
 */
class ProductIngredient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productIngredients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $productId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ingredient")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ingredientId;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?Product
    {
        return $this->productId;
    }

    public function setProductId(?Product $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getIngredientId(): ?Ingredient
    {
        return $this->ingredientId;
    }

    public function setIngredientId(?Ingredient $ingredientId): self
    {
        $this->ingredientId = $ingredientId;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
