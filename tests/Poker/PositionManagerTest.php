<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class PositionManager.
 */
class PositionManagerTest extends TestCase
{
    private PositionManager $manager;
    
    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->manager = new PositionManager();
    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreatePositionManager()
    {
        $this->assertInstanceOf("\App\Poker\PositionManager", $this->manager);
    }

    /**
     * Assign initial positions and then move.
     */
    public function testPositionsInitAndUpdate(): void
    {
        $player1 = new Player();
        $player2 = new Player();
        $player3 = new Player();

        $playerArray = [$player1, $player2, $player3];
        $this->manager->assignPositions($playerArray);

        $initP1Pos = $playerArray[0]->getPosition();
        $initP2Pos = $playerArray[1]->getPosition();
        $initP3Pos = $playerArray[2]->getPosition();

        $this->assertNotEquals($initP1Pos, $initP2Pos);
        $this->assertNotEquals($initP1Pos, $initP3Pos);
        $this->assertNotEquals($initP2Pos, $initP3Pos);

        $this->manager->updatePositions($playerArray);

        $newP1Pos = $playerArray[0]->getPosition();
        $newP2Pos = $playerArray[1]->getPosition();
        $newP3Pos = $playerArray[2]->getPosition();

        $this->assertNotEquals($newP1Pos, $newP2Pos);
        $this->assertNotEquals($newP1Pos, $newP3Pos);
        $this->assertNotEquals($newP2Pos, $newP3Pos);

        $this->assertNotEquals($newP1Pos, $initP1Pos);
        $this->assertNotEquals($newP2Pos, $initP2Pos);
        $this->assertNotEquals($newP3Pos, $initP3Pos);
    }

    /**
     * Sort array on lowest position value.
     */
    public function testSortByPosition(): void
    {
        $player1 = new Player();
        $player2 = new Player();
        $player3 = new Player();

        $playerArray = [$player1, $player2, $player3];
        $this->manager->assignPositions($playerArray);
        $this->manager->updatePositions($playerArray);
        $sortedPlayers = $this->manager->sortPlayersByPosition($playerArray);

        $lowest = $sortedPlayers[0]->getPosition();
        $middle = $sortedPlayers[1]->getPosition();
        $highest = $sortedPlayers[2]->getPosition();

        $this->assertEquals(0, $lowest);
        $this->assertEquals(1, $middle);
        $this->assertEquals(2, $highest);


        $this->assertLessThan($middle, $lowest, 'The first player should have the lowest position.');
        $this->assertLessThan($highest, $middle, 'The second player should have a lower position than the third player.');
        $this->assertGreaterThan($lowest, $middle, 'The second player should have a higher position than the first player.');
        $this->assertGreaterThan($middle, $highest, 'The third player should have the highest position.');
    }


    /**
     * Assert that last laspPlayer is correctly identified.
     */
    public function testPlayerIsLast(): void
    {
        $player1 = new Player();
        $player2 = new Player();
        $player3 = new Player();

        $playerArray = [$player1, $player2, $player3];
        $this->manager->assignPositions($playerArray);

        $res1 = $this->manager->playerIsLast($player1, $playerArray);
        $res2 = $this->manager->playerIsLast($player2, $playerArray);
        $res3 = $this->manager->playerIsLast($player3, $playerArray);

        $this->assertFalse($res2);
        $this->assertFalse($res2);
        $this->assertTrue($res3);
    }
}