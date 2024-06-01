<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;
// use App\Poker\Hero;
/**
 * Test cases for class OpponentActionManager.
 */
class OpponentActionManagerTest extends TestCase
{
    private OpponentActionManager $manager;
    private Opponent $opponent;
    
    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->manager = new OpponentActionManager();
        $this->opponent = new Opponent();
        
    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateOpponentActionManager()
    {
        $this->assertInstanceOf("\App\Poker\OpponentActionManager", $this->manager);
    }
}