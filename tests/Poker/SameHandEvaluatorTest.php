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

    /**
     * Test One pair hands.
     */
    public function testOnePairComparison(): void
    {

        // Third hand should win with strongest kicker
        $testOne = [
            [7,10,9,2,5,3,5],
            [7,10,9,2,5,3,10],
            [14,10,9,2,5,3,10]
        ];
        // First hand should win with strongest pair
        $testTwo = [
            [7,10,14,14,5,3,2],
            [7,10,9,2,5,3,10],
            [14,10,9,2,5,3,10]
        ];

        $resultOne = $this->evaluator->compareOnePair($testOne);
        $resultTwo = $this->evaluator->compareOnePair($testTwo);

        $exp = 2;
        $this->assertEquals($exp, $resultOne);
        $exp = 0;
        $this->assertEquals($exp, $resultTwo);

    }

    /**
     * Test two pair hands.
     */
    public function testTwoPairComparison(): void
    {

        // second hand hand should win with strongest kicker
        $testOne = [
            [7,10,10,2,5,3,5],
            [7,10,10,14,5,3,5],
            [2,10,9,2,5,3,10]
        ];
        // First hand should win with strongest 2pair
        $testTwo = [
            [7,10,14,14,5,3,10],
            [7,10,9,2,2,3,10],
            [14,14,9,2,5,3,2]
        ];

        $resultOne = $this->evaluator->compareTwoPair($testOne);
        $resultTwo = $this->evaluator->compareTwoPair($testTwo);

        $exp = 1;
        $this->assertEquals($exp, $resultOne);
        $exp = 0;
        $this->assertEquals($exp, $resultTwo);
    }

    /**
     * Test trip hands.
     */
    public function testTripComparison(): void
    {

        // second hand hand should win with strongest kicker
        $testOne = [
            [7,10,10,10,4,3,5],
            [14,10,10,10,5,3,5],
            [2,10,9,2,5,3,10]
        ];
        // First hand should win with strongest 2pair
        $testTwo = [
            [7,14,14,14,5,3,10],
            [7,10,9,10,2,3,10],
            [2,14,9,2,5,3,2]
        ];

        $resultOne = $this->evaluator->compareTrips($testOne);
        $resultTwo = $this->evaluator->compareTrips($testTwo);

        $exp = 1;
        $this->assertEquals($exp, $resultOne);
        $exp = 0;
        $this->assertEquals($exp, $resultTwo);
    }

    /**
     * Test quad hands.
     */
    public function testQuadsComparison(): void
    {

        // second hand hand should win with strongest kicker
        $testOne = [
            [10,10,10,10,4,3,5],
            [14,10,10,10,10,3,5],
            [2,2,2,2,5,3,10]
        ];
        // First hand should win with strongest quads
        $testTwo = [
            [7,14,14,14,5,3,14],
            [7,10,9,10,10,3,10],
            [2,14,2,2,5,3,2]
        ];

        $resultOne = $this->evaluator->compareQuads($testOne);
        $resultTwo = $this->evaluator->compareQuads($testTwo);

        $exp = 1;
        $this->assertEquals($exp, $resultOne);
        $exp = 0;
        $this->assertEquals($exp, $resultTwo);
    }


    /**
     * Test full houses hands.
     */
    public function testFullHousesComparison(): void
    {

        // second hand hand should win with strongest full
        $testOne = [
            [10,10,10,5,5,3,2],
            [14,10,10,10,4,14,5],
            [2,2,2,2,5,3,10]
        ];
        // First hand should win with strongest full
        $testTwo = [
            [7,14,14,14,5,3,7],
            [7,7,7,14,14,3,10],
            [2,5,2,2,5,3,2]
        ];

        $resultOne = $this->evaluator->compareFullHouses($testOne);
        $resultTwo = $this->evaluator->compareFullHouses($testTwo);

        $exp = 1;
        $this->assertEquals($exp, $resultOne);
        $exp = 0;
        $this->assertEquals($exp, $resultTwo);
    }

    /**
     * Test flush hands.
     */
    public function testFlushesComparison(): void
    {

        // Second hand should win with the strongest flush
        $ranks = [
            [1, 2, 3, 4, 5, 6, 7],
            [1, 2, 3, 4, 5, 6, 8],
        ];
    
        $suits = [
            ['hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts'],
            ['hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts'],
        ];
    
        $result = $this->evaluator->compareFlushes($ranks, $suits);
    
        $exp = 1;
        $this->assertEquals($exp, $result);

        // First hand should win with the strongest flush
        $ranks = [
            [1, 2, 3, 4, 5, 6, 7],
            [1, 2, 3, 4, 5, 6, 8],
        ];
    
        $suits = [
            ['hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts'],
            ['hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'clubs'],
        ];
        
        $result = $this->evaluator->compareFlushes($ranks, $suits);
    
        $exp = 0;
        $this->assertEquals($exp, $result);

    // Third hand should win with the strongest flush
    $ranks = [
        [1, 2, 3, 4, 5, 6, 7],  // Flush 1
        [1, 2, 3, 4, 5, 6, 8],  // Flush 2
        [2, 3, 4, 5, 6, 7, 9],  // Flush 3
    ];

    $suits = [
        ['hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts'],
        ['hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts'],
        ['hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts', 'hearts'],
    ];

    $result = $this->evaluator->compareFlushes($ranks, $suits);


    $exp = 2;
    $this->assertEquals($exp, $result);
    }

    /**
     * Test straight hands.
     */
    public function testStraightsComparison(): void
    {
            // Second hand should win with the strongest flush
    $ranks = [
        [1, 2, 3, 4, 5, 6, 7],
        [1, 2, 10, 11, 12, 13, 14],
        [2, 3, 4, 5, 6, 7, 9],
    ];



    $result = $this->evaluator->compareStraights($ranks);


    $exp = 1;
    $this->assertEquals($exp, $result);
    }

}