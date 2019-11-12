<?php


namespace App\Transaction\Service;


use App\Entity\OrderTransaction;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService
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
     * @param OrderTransaction $transaction
     */
    public function persistTransaction(OrderTransaction $transaction) : void
    {
        $this->entityManager->persist($transaction);
    }

    /**
     * @return OrderTransaction[]
     */
    public function getAllTransactions() : array
    {
        return $this->entityManager->getRepository(OrderTransaction::class)->findAll();
    }
}