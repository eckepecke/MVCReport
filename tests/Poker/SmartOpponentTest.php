<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;
use App\Poker\CardHand;


/**
 * Test cases for class SmartOpponent.
 */
class SmartOpponentTest extends TestCase
{

    /**
     * @var SmartOpponent
     */
    private $player;

    private $handMock;

        /**
     * Set up the test environment before each test method.
     */
    public function setUp(): void
    {
        $this->player = new SmartOpponent();
        $this->handMock = $this->createMock(CardHand::class);
        $this->player->addHand($this->handMock);
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreatePlayer()
    {
        $this->assertInstanceOf("\App\Poker\SmartOpponent", $this->player);
    }

    /**
     * Test the response to bet method with hand strength greater than 1.
     */
    public function testResponseToBetStrongHand()
    {
        $this->handMock->method('getStrengthInt')->willReturn(2);

        $decision = $this->player->responseToBet();

        $this->assertContains($decision, ['call', 'raise']);
    }

    /**
     * Test the response to bet method with hand strength of 1 or less.
     */
    public function testResponseToBetWeakHand()
    {
        $this->handMock->method('getStrengthInt')->willReturn(1);

        $decision = $this->player->responseToBet();

        $this->assertContains($decision, ['fold', 'call', 'raise']);
    }

    /**
     * Test the actionVsCheck method with hand strength greater than 1.
     */
    public function testActionVsCheckStrongHand()
    {
        $this->handMock->method('getStrengthInt')->willReturn(2);

        $decision = $this->player->actionVsCheck();

        $this->assertContains($decision, ['check', 'bet']);
        $this->assertContains($decision, ['check', 'bet', 'bet', 'bet']);
    }

    /**
     * Test the actionVsCheck method with hand strength of 1 or less.
     */
    public function testActionVsCheckWeakHand()
    {
        $this->handMock->method('getStrengthInt')->willReturn(1);

        $decision = $this->player->actionVsCheck();

        $this->assertContains($decision, ['check', 'bet']);
    }

    /**
     * Test the chooseBetSize method with non-zero pot size.
     */
    public function testChooseBetSizeNonZeroPot()
    {
        $potSize = 1000;
        $expectedBetSize = 0.75 * $potSize;

        $betSize = $this->player->chooseBetSize($potSize);

        $this->assertEquals($expectedBetSize, $betSize);
    }

    /**
     * Test the chooseBetSize method with zero pot size.
     */
    public function testChooseBetSizeZeroPot()
    {
        $potSize = 0;
        $expectedBetSize = 800;

        $betSize = $this->player->chooseBetSize($potSize);

        $this->assertEquals($expectedBetSize, $betSize);
    }


    /**
     * Test the actionVsShove method with hand strength greater than 1.
     */
    public function testActionVsShoveStrongHand()
    {
        $this->handMock->method('getStrengthInt')->willReturn(2);

        $decision = $this->player->actionVsShove();

        $this->assertEquals('call', $decision);
    }

    /**
     * Test the actionVsShove method with hand strength of 1 or less.
     */
    public function testActionVsShoveWeakHand()
    {
        $this->handMock->method('getStrengthInt')->willReturn(1);

        $decision = $this->player->actionVsShove();

        $this->assertContains($decision, ['fold', 'call']);
    }
}