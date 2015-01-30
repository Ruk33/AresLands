<?php namespace Libraries;

use Item;

class ItemBag
{
    /**
     * @var \Item
     */
    protected $item;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param integer $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount > 0 ? $amount : 0;
    }

    /**
     * @param Item $item
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
        $this->amount = 0;
    }
}