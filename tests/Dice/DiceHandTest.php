<?php

namespace App\Dice;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Dice.
 */
class DiceHandTest extends TestCase
{
    /**
     * Stub the dices to assure the value can be asserted.
     */
    public function testAddStubbedDices()
    {
        // Create a stub for the Dice class.
        $stub = $this->createMock(Dice::class);

        // Configure the stub.
        $stub->method('roll')
            ->willReturn(6);
        $stub->method('getValue')
            ->willReturn(6);

        $dicehand = new DiceHand();
        $dicehand->add(clone $stub);
        $dicehand->add(clone $stub);
        $dicehand->roll();
        $res = $dicehand->sum();
        $this->assertEquals(12, $res);
    }

    /**
     * Make sure Hand has the right aoumnt of die.
     */
    public function testGetNumberDices()
    {
        $hand = new DiceHand();
        $initial = $hand->getNumberDices();
        $exp = 0;

        $this->assertEquals($exp, $initial);
        $hand->add(new Dice());

        $addedOne = $hand->getNumberDices();
        $exp = 1;

        $this->assertEquals($exp, $addedOne);

        $hand->add(new Dice());
        $hand->add(new Dice());
        $hand->add(new Dice());

        $addedFour = $hand->getNumberDices();
        $exp = 4;

        $this->assertEquals($exp, $addedFour);
    }

    public function testGetString()
    {

        $die1 = $this->createMock(Dice::class);
        $die1->method('getAsString')->willReturn('1');

        $die2 = $this->createMock(Dice::class);
        $die2->method('getAsString')->willReturn('2');

        $hand = new DiceHand();
        $hand->add($die1);
        $hand->add($die2);

        $result = $hand->getString();
        $exp =['1', '2'];

        $this->assertEquals($exp, $result);
    }
}