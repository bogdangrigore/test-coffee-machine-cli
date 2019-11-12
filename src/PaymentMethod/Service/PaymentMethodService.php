<?php


namespace App\PaymentMethod\Service;


use App\Entity\PaymentMethod;
use Doctrine\ORM\EntityManagerInterface;

class PaymentMethodService
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
     * @return array
     */
    public function getPaymentMethodByName(string $name) : array
    {
        return $this->entityManager->getRepository(PaymentMethod::class)->findBy(['name' => $name]);
    }

}