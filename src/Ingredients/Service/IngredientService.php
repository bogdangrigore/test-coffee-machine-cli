<?php


namespace App\Ingredients\Service;

use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;

class IngredientService
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
     * @param string $name
     * @param int $quantity
     */
    public function update(string $name, int $quantity) : void
    {
        /** @var Ingredient $ingredient */
        $ingredient = $this->entityManager->getRepository(Ingredient::class)->findBy(['name' => $name])[0];

        if (!$ingredient) {
            throw new \InvalidArgumentException('Ingredient with that name does not exist.');
        }

        $ingredient->setQuantity($quantity);
        $this->entityManager->persist($ingredient);
    }

    /**
     * @param $id
     * @param int $quantity
     */
    public function reduceQuantity($id, int $quantity) : void
    {
        /** @var Ingredient $ingredient */
        $ingredient = $this->entityManager->getRepository(Ingredient::class)->find($id);
        if (!$ingredient) {
            throw new \InvalidArgumentException('Ingredient with that ID does not exist.');
        }
        $result = $ingredient->getQuantity() - $quantity;
        if ($result < 0) {
            throw new \RuntimeException('Cannot reduce quantity of the ingredient below zero');
        }

        $ingredient->setQuantity($result);
        $this->entityManager->persist($ingredient);
    }

    /**
     * @return object[]
     */
    public function getAllIngredients() : object
    {
        $ingredientsRepository = $this->entityManager->getRepository(Ingredient::class);

        return $ingredientsRepository->findAll();
    }
}