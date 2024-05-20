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
}