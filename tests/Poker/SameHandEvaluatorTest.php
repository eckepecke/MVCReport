<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;


/**
 * Test cases for class game.
 */
class SameHandEvaluatorTest extends TestCase
{

    private SameHandEvaluator $evaluator;

    protected function setUp(): void
    {
        $this->evaluator = new SameHandEvaluator();
        // $player1 = new Player();
        // $player2 = new Player();

    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateGame(): void
    {
        $this->assertInstanceOf("\App\Poker\SameHandEvaluator", $this->evaluator);
    }

}