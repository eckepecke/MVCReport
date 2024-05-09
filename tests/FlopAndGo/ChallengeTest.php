<?php

namespace App\FlopAndGo;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Challenge.
 */
class ChallengeTest extends TestCase
{
    /**
     * @var Challenge
     */
    private $challenge;

    /**
     * Set up the test environment before each test method.
     */
    public function setUp(): void
    {
        $this->challenge = new Challenge(4);
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateChallenge()
    {
        $challenge = new Challenge();
        $this->assertInstanceOf("\App\FlopAndGo\Challenge", $challenge);
    }

    /**
     * Increment hands played and see if hands played property updates correctly.
     */
    public function testIncrementHandsPlayed()
    {
        $this->challenge->incrementHandsPlayed();
        $this->assertEquals(1, $this->challenge->getHandsPlayed());
    }

    /**
     * Check that challengeComplete() correctly signals when all hands have been played.
     */
    public function testChallengeComplete()
    {
        $this->challenge->incrementHandsPlayed();
        $this->challenge->incrementHandsPlayed();
        $this->challenge->incrementHandsPlayed();
        $this->assertFalse($this->challenge->challengeComplete());

        $this->challenge->incrementHandsPlayed();
        $this->assertTrue($this->challenge->challengeComplete());
    }

    /**
     * Check that duration property is correctly initiated
     */
    public function testDuration()
    {
        $this->challenge->getDuration();
        $this->assertEquals(4, $this->challenge->getDuration());
    }

    /**
     * Check that setting and getting handwinner string works.
     */
    public function testHandWinner()
    {
        $this->challenge->setHandWinner("Erik");
        $res = $this->challenge->getHandWinner();
        $exp = "Erik";
        $this->assertSame($exp, $res);
    }
}