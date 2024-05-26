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
        $player1 = new Player();
        $player2 = new Player();

    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateGame(): void
    {
        $this->assertInstanceOf("\App\Poker\SameHandEvaluator", $this->evaluator);
    }

    public function testHighCardComparison(): void
    {
        // Create mock player objects for testing
        $playerToBeat = $this->createMock(Player::class);
        $playerToBeat->method('getName')->willReturn('Player A');
        $playerToBeatHand = $this->createMock(Hand::class);
        $playerToBeat->method('getHand')->willReturn($playerToBeatHand);

        $challenger = $this->createMock(Player::class);
        $challenger->method('getName')->willReturn('Player B');
        $challengerHand = $this->createMock(Hand::class);
        $challenger->method('getHand')->willReturn($challengerHand);

        // Mock the method extractRanksAndSuits to return specific values
        $yourClassNameMock = $this->getMockBuilder(YourClassName::class)
            ->onlyMethods(['extractRanksAndSuits'])
            ->getMock();

        // Define the return values for the mocked method
        $yourClassNameMock->expects($this->exactly(2))
            ->method('extractRanksAndSuits')
            ->willReturnOnConsecutiveCalls(
                [[10, 8, 6, 4, 2], ['Hearts', 'Diamonds', 'Clubs', 'Spades', 'Hearts']],
                [[9, 7, 5, 3, 1], ['Hearts', 'Diamonds', 'Clubs', 'Spades', 'Hearts']]
            );

        // Set up expectations for the player object
        $playerToBeat->expects($this->once())->method('getName')->willReturn('Player A');
        $challenger->expects($this->once())->method('getName')->willReturn('Player B');

        // Call the method under test
        $result = $yourClassNameMock->compareHighCard($playerToBeat, $challenger);

        // Assert the result
        $this->assertSame($challenger, $result);
    }
}