<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;


/**
 * Test cases for class Player.
 */
class HeroTest extends TestCase
{

    /**
     * @var Hero
     */
    private $hero;


        /**
     * Set up the test environment before each test method.
     */
    public function setUp(): void
    {
        $this->hero = new Hero();
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreatePlayer()
    {
        $this->assertInstanceOf("\App\Poker\Hero", $this->hero);
    }

    /**
     * Test the raise method.
     */
    public function testRaise()
    {
        // Set initial values
        $bet = 200;
        
        // Perform the raise
        $this->hero->raise($bet);

        // Calculate expected values
        $expectedRaise = $bet * 2;
        $expectedCurrentBet = $expectedRaise;
        $expectedStack = 1600;
        $expectedLastAction = "raise";
        $expectedAllIn = false;

        // Assert the values
        $this->assertEquals($expectedCurrentBet, $this->hero->getCurrentBet());
        $this->assertEquals($expectedStack, $this->hero->getStack());
        $this->assertEquals($expectedLastAction, $this->hero->getLastAction());
        $this->assertEquals($expectedAllIn, $this->hero->isAllIn());
    }
}