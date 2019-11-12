<?php


namespace App\Products\Service;


use App\Entity\Ingredient;
use App\Entity\Product;
use App\Ingredients\Service\IngredientService;
use Doctrine\ORM\EntityManagerInterface;

class ProductsService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param bool $onlyAvailable
     * @return Product[]
     */
    public function getAllProducts(bool $onlyAvailable = false) : array
    {
        $productsRepository = $this->entityManager->getRepository(Product::class);
        /** @var Product[] $products */
        $products = $productsRepository->findAll();

        if (!$onlyAvailable) {
            return $products;
        }

        $availableProducts = [];

        foreach ($products as $product) {
            if ($this->checkProductIngredientsAvailability($product, 1)) {
                $availableProducts[] = $product;
            }
        }

        return $availableProducts;
    }

    /**
     * @param string $name
     * @return object[]
     */
    public function getProductsByName(string $name) : object
    {
        return $this->entityManager->getRepository(Product::class)->findBy(['name' => $name]);
    }

    /**
     * @param $id
     * @return Product
     */
    public function getProductById($id) : Product
    {
        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        return $product;
    }

    /**
     * @param $id
     * @param int $productQuantity
     * @return bool
     */
    public function isProductAvailable($id, int $productQuantity) : bool
    {
        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return false;
        }

        return $this->checkProductIngredientsAvailability($product, $productQuantity);
    }

    /**
     * @param Product $product
     * @param int $productQuantity
     * @return bool
     */
    private function checkProductIngredientsAvailability(Product $product, int $productQuantity) : bool
    {
        $productIngredients = $product->getProductIngredients();
        foreach ($productIngredients as $productIngredient) {
            /** @var Ingredient $machineIngredient */
            $machineIngredient = $this->entityManager->find(
                Ingredient::class, $productIngredient->getIngredientId()
            );
            if ($machineIngredient->getQuantity() - $productIngredient->getQuantity() * $productQuantity <= 0) {
                return false;
            }
        }

        return true;
    }
}