<?php

namespace App\Dice;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Dice.
 */
class DiceTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateDice()
    {
        $die = new Dice();
        $this->assertInstanceOf("\App\Dice\Dice", $die);

        $res = $die->getAsString();
        $this->assertNotEmpty($res);
    }

    /**
     * Create a mocked object that always returns 6.
     */
    public function testStubRollDiceLastRoll()
    {
        // Create a stub for the Dice class.
        $stub = $this->createMock(Dice::class);

        // Configure the stub.
        $stub->method('roll')
            ->willReturn(6);

        $res = $stub->roll();
        $exp = 6;
        $this->assertEquals($exp, $res);
    }

    /**
     * Test roll returns value in valid range.
     */
    public function testRoll()
    {
        $dice = new Dice();

        for ($i = 0; $i < 10; $i++) {
            $result = $dice->roll();
            $this->assertIsInt($result, 'The roll result should be an integer');
            $this->assertGreaterThanOrEqual(1, $result, 'The roll result should be at least 1');
            $this->assertLessThanOrEqual(6, $result, 'The roll result should be at most 6');
        }
    }

        /**
     * Test roll returns value in valid range.
     */
    public function testGetValue()
    {
        $dice = new Dice();

        $valueFromRoll = $dice->roll();
        $valueFromGet = $dice->getValue();

        $this->assertEquals($valueFromGet, $valueFromRoll);
    }
}