<?php

namespace App\Monetary\Service;

use App\Entity\Cash;
use App\Monetary\Model\PriceBreakdown;
use Doctrine\ORM\EntityManagerInterface;

class BalanceService
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
     * @param int $productPrice
     * @return bool
     */
    public function canGiveBackChange(int $productPrice) : bool
    {
        $priceBreakdown = $this->getPriceBreakdown($productPrice);

        return $priceBreakdown->getMachineRonTotal()->getQuantity() - $priceBreakdown->getPriceRonValue() >=0 &&
            $priceBreakdown->getMachineBanTotal()->getQuantity() - $priceBreakdown->getPriceBanValue() >= 0;
    }

    /**
     * @param int $paymentAmountRon
     * @param int $paymentAmountBan
     * @param int $productPrice
     * @param int $productQuantity
     */
    public function giveOutChange(int $paymentAmountRon, int $paymentAmountBan, int $productPrice, int $productQuantity) : void
    {
        $providedAmount = $paymentAmountRon * 100 + $paymentAmountBan;
        if (!$this->canGiveBackChange($providedAmount - $productPrice * $productQuantity)) {
            throw new \InvalidArgumentException('Cannot reduce the value of balance below zero');
        }

        $priceBreakdown = $this->getPriceBreakdown($providedAmount - $productPrice * $productQuantity);
        $ronTotal = $priceBreakdown->getMachineRonTotal();
        $ronTotal->setQuantity($ronTotal->getQuantity() - $priceBreakdown->getPriceRonValue());
        $banTotal = $priceBreakdown->getMachineBanTotal();
        $banTotal->setQuantity($banTotal->getQuantity() - $priceBreakdown->getPriceBanValue());
        $this->entityManager->persist($ronTotal);
        $this->entityManager->persist($banTotal);
    }

    public function addCashToBalance(int $paymentAmountRon, int $paymentAmountBan)
    {
        $providedAmount = $paymentAmountRon * 100 + $paymentAmountBan;
        $priceBreakdown = $this->getPriceBreakdown($providedAmount);
        $ronTotal = $priceBreakdown->getMachineRonTotal();
        $ronTotal->setQuantity($ronTotal->getQuantity() + $priceBreakdown->getPriceRonValue());
        $banTotal = $priceBreakdown->getMachineBanTotal();
        $banTotal->setQuantity($banTotal->getQuantity() + $priceBreakdown->getPriceBanValue());
        $this->entityManager->persist($ronTotal);
        $this->entityManager->persist($banTotal);
    }

    private function getPriceBreakdown(int $price) : PriceBreakdown
    {
        $productRonValue = (int)($price / 100);
        $productBanValue = (int)($price % 100);

        /** @var Cash[] $machineRonTotal */
        $machineRonTotal = $this->entityManager->getRepository(Cash::class)->findBy(['name' => 'ron'])[0];
        $machineBanTotal = $this->entityManager->getRepository(Cash::class)->findBy(['name' => 'ban'])[0];

        return PriceBreakdown::fromArray([
            'priceRonValue' => $productRonValue,
            'priceBanValue' => $productBanValue,
            'machineRonTotal' => $machineRonTotal,
            'machineBanTotal' => $machineBanTotal,
        ]);
    }
}