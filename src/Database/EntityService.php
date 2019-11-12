<?php


namespace App\Database;


use Doctrine\ORM\EntityManagerInterface;

class EntityService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function commitEntities() : void
    {
        $this->entityManager->flush();
    }
}