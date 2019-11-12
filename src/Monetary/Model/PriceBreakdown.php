<?php


namespace App\Monetary\Model;


use App\Entity\Cash;

class PriceBreakdown
{
    /**
     * @var int
     */
    private $priceRonValue;
    /**
     * @var int
     */
    private $priceBanValue;
    /**
     * @var Cash
     */
    private $machineRonTotal;
    /**
     * @var Cash
     */
    private $machineBanTotal;

    private function __construct(int $priceRonValue, int $priceBanValue, Cash $machineRonTotal, Cash $machineBanTotal)
    {
        $this->priceRonValue = $priceRonValue;
        $this->priceBanValue = $priceBanValue;
        $this->machineRonTotal = $machineRonTotal;
        $this->machineBanTotal = $machineBanTotal;
    }

    public static function fromArray(array $data)
    {
        return new self($data['priceRonValue'], $data['priceBanValue'], $data['machineRonTotal'], $data['machineBanTotal']);
    }

    /**
     * @return int
     */
    public function getPriceRonValue(): int
    {
        return $this->priceRonValue;
    }

    /**
     * @return int
     */
    public function getPriceBanValue(): int
    {
        return $this->priceBanValue;
    }

    /**
     * @return Cash
     */
    public function getMachineRonTotal(): Cash
    {
        return $this->machineRonTotal;
    }

    /**
     * @return Cash
     */
    public function getMachineBanTotal(): Cash
    {
        return $this->machineBanTotal;
    }
}