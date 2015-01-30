<?php namespace Tests;

use Libraries\ItemBag;
use Mockery;

/**
 * @group ItemBag
 */
class ItemBagTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetItem()
    {
        $item = Mockery::mock("\\Item");
        $itemBag = new ItemBag($item);

        $this->assertEquals($item, $itemBag->getItem());
    }

    public function testSetGetAmount()
    {
        $item = Mockery::mock("\\Item");
        $itemBag = new ItemBag($item);

        $this->assertEquals(0, $itemBag->getAmount());

        $itemBag->setAmount(5);
        $this->assertEquals(5, $itemBag->getAmount());

        $itemBag->setAmount(-5);
        $this->assertEquals(0, $itemBag->getAmount());

        $itemBag->setAmount("s");
        $this->assertEquals(0, $itemBag->getAmount());
    }
} 