<?php

namespace App\FlopAndGo;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Villain.
 */
class VillainTest extends TestCase
{

    /**
     * @var Villain
     */
    private $villain;

    /**
     * Set up the test environment before each test method.
     */
    public function setUp(): void
    {
        $this->villain = new Villain();
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateDealer()
    {
        $this->assertInstanceOf("\App\FlopAndGo\Villain", $this->villain);
    }

    /**
     * Test that the expected name is returned.
     */
    public function testGetName()
    {
        $res = $this->villain->getName();
        $exp = "Teddy KGB";

        $this->assertSame($exp, $res);
    }

    /**
     * Test that the correct betsize is returned.
     */
    public function testRaiseNoCurrentBet()
    {
        $heroBet = 500;
        $res = $this->villain->raise($heroBet);
        $exp = 1500;

        $this->assertSame($exp, $res);
    }

    /**
     * Test that the correct betsize is returned.
     */
    public function testRaiseWithCurrentBet()
    {
        $this->villain->bet(100);
        $heroRaise = 500;
        $res = $this->villain->raise($heroRaise);
        $exp = 1500;

        $this->assertSame($exp, $res);
    }

    /**
     * Test that the correct betsize is returned when raise is bigger than stack.
     */
    public function testRaiseWithNotEnoughChips()
    {
        $heroRaise = 4000;
        $res = $this->villain->raise($heroRaise);
        $exp = 5000;

        $this->assertSame($exp, $res);
        $this->assertNotEquals(12000, $res);
    }
}
