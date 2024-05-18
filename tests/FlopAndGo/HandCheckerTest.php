<?php

namespace App\FlopAndGo;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Challenge.
 */
class HandCheckerTest extends TestCase
{
    private $handChecker;

    /**
     * Set up the test environment before each test method.
     */
    public function setUp(): void
    {
        $this->handChecker = new HandChecker();
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateChallenge()
    {
        $this->assertInstanceOf("\App\FlopAndGo\HandChecker", $this->handChecker);
    }

    /**
     * Check for trips.
     */
    public function testCheckForTrips(): void
    {
        $noTrips = [
            2 => 1,
            3 => 1,
            4 => 1,
            5 => 1,
            6 => 1,
        ];

        $hasTrips = [
            2 => 1,
            3 => 1,
            4 => 3
        ];

        $this->handChecker->checkForTrips($noTrips);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Three of a kind'];

        $this->assertFalse($bool);

        $this->handChecker->checkForTrips($hasTrips);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Three of a kind'];

        $this->assertTrue($bool);
    }

    /**
     * Check for wheelstraight.
     */
    public function testCheckWheelStraight(): void
    {
        $noStraight = [5, 5, 5, 5, 5];

        $hasWheel = [14, 2, 3, 4, 5];

        $this->handChecker->checkForStraight($noStraight);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Straight'];

        $this->assertFalse($bool);

        $this->handChecker->checkForStraight($hasWheel);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Straight'];

        $this->assertTrue($bool);
    }

    /**
     * Check for full.
     */
    public function testCheckForFull(): void
    {
        $noFull = [
            2 => 1,
            3 => 1,
            4 => 3
        ];

        $hasFull = [
            2 => 2,
            4 => 3
        ];

        $this->handChecker->checkForFullHouse($noFull);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Full house'];

        $this->assertFalse($bool);

        $this->handChecker->checkForFullHouse($hasFull);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Full house'];

        $this->assertTrue($bool);
    }

    /**
     * Check for quads.
     */
    public function testCheckQuads(): void
    {
        $noQuads = [
            2 => 1,
            3 => 1,
            4 => 3
        ];

        $hasQuads1 = [
            2 => 0,
            4 => 5
        ];

        $hasQuads2 = [
            2 => 1,
            4 => 4
        ];

        $this->handChecker->checkForQuads($noQuads);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Four of a kind'];

        $this->assertFalse($bool);

        $this->handChecker->checkForQuads($hasQuads1);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Four of a kind'];

        $this->assertTrue($bool);

        $this->handChecker->checkForQuads($hasQuads2);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Four of a kind'];

        $this->assertTrue($bool);
    }

    /**
     * Check for pairs.
     */
    public function testCheckForPairs(): void
    {
        $noPairs = [
            2 => 1,
            3 => 1,
            4 => 1,
            5 => 1,
            6 => 1,
        ];

        $hasOnePair = [
            2 => 1,
            3 => 1,
            4 => 2,
            5 => 1
        ];

        $hasTwoPair = [
            2 => 1,
            3 => 1,
            4 => 2,
            5 => 2
        ];

        $this->handChecker->checkForPairs($noPairs);
        $data = $this->handChecker->getStrengthArray();
        $bool1 = $data['One pair'];
        $bool2 = $data['Two pair'];

        $this->assertFalse($bool1);
        $this->assertFalse($bool2);

        $this->handChecker->checkForPairs($hasOnePair);
        $data = $this->handChecker->getStrengthArray();
        $bool1 = $data['One pair'];
        $bool2 = $data['Two pair'];

        $this->assertTrue($bool1);
        $this->assertFalse($bool2);

        $this->handChecker->checkForPairs($hasTwoPair);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Two pair'];

        $this->assertTrue($bool);
    }

    /**
     * Check for flush.
     */
    public function testCheckForFlush(): void
    {
        $noFlush = 4;
        $flush1 = 5;
        $flush2 = 6;

        $this->handChecker->checkForFlush($noFlush);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Flush'];
        $this->assertFalse($bool);

        $this->handChecker->checkForFlush($flush1);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Flush'];
        $this->assertTrue($bool);

        $this->handChecker->resetStrengthArray();

        $this->handChecker->checkForFlush($flush2);
        $data = $this->handChecker->getStrengthArray();
        $bool = $data['Flush'];
        $this->assertTrue($bool);

    }
}