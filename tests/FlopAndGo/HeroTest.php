<?php

namespace App\FlopAndGo;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Hero.
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
    public function testCreateDealer()
    {
        $this->assertInstanceOf("\App\FlopAndGo\Hero", $this->hero);
    }

    /**
     * Test that the expected name is returned.
     */
    public function testGetName()
    {
        $res = $this->hero->getName();
        $exp = "Mike";

        $this->assertSame($exp, $res);
    }

    /**
     * Test that the expected stack is returned.
     */
    public function testGetStack()
    {


        $res = $this->hero->getStack();
        $exp = 5000;
        $this->assertSame($exp, $res);

        $this->hero->bet(1000);
        $res = $this->hero->getStack();
        $exp = 4000;
        $this->assertSame($exp, $res);

        $res = $this->hero->getStartStack();
        $exp = 5000;
        $this->assertSame($exp, $res);
    }

    public function testGetStrengthAfterUpdate()
    {

        $boolValues = [
            'High Card' => false,
            'One Pair' => false,
            'Two Pair' => false,
            'Three of a Kind' => false,
            'Straight' => true,
            'Flush' => false,
            'Full House' => false,
            'Four of a Kind' => false,
            'Straight Flush' => false,
            'Royal Flush' => false,
        ];

        $this->hero->updateStrength($boolValues);

        $this->assertEquals('Straight', $this->hero->getStrength());
    }

}
