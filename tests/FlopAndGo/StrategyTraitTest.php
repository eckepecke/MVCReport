<?php

namespace App\FlopAndGo;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for trait StrategyTrait.
 */
class StrategyTraitTest extends TestCase
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
     * Test that returned betsize is within valid range.
     */
    public function testRandbetSize()
    {
        $potSize = 1000;
        $betSize = $this->villain->randBetSize($potSize);

        // Calculate expected bet sizes
        $expectedSizes = [
            0.33 * $potSize,
            0.75 * $potSize,
            1.5 * $potSize
        ];

        // Assert that the bet size is one of the expected values
        $this->assertContains($betSize, $expectedSizes);
    }

    /**
     * Test that returned actionFacingBet() returns a valid action.
     */
    public function testRandActionFacingBet()
    {
    
        $action = $this->villain->actionFacingBet();

        $expectedActions = [
            "call",
            "fold",
            "raise"
        ];

        $this->assertContains($action, $expectedActions);
    }

    /**
     * Test that returned betsizes is 75% of pot.
     */
    public function testBetVsCheck()
    {
        $potSize = 1000;
        $betSize = $this->villain->betVSCheck($potSize);

        $expectedSize = 0.75 * $potSize;

        $this->assertSame($betSize, $expectedSize);
    }

    /**
     * Test that returned actionVsCheck() returns a valid action.
     */
    public function testActionFacingCheck()
    {
    
        $action = $this->villain->actionVsCheck();

        $expectedActions = [
            "check",
            "bet"
        ];

        $this->assertContains($action, $expectedActions);
    }

        /**
     * Test that returned betOpportunity() returns a valid action.
     */
    public function testBetOpportunity()
    {
    
        $action = $this->villain->betOpportunity();

        $expectedActions = [
            "check",
            "bet"
        ];

        $this->assertContains($action, $expectedActions);
    }
}