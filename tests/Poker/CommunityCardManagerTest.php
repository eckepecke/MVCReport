<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class CommunityCardManager.
 */
class CommunityCardManagerTest extends TestCase
{
    private CommunityCardManager $manager;
    
    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->manager = new CommunityCardManager();
    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateCCManager()
    {
        $this->assertInstanceOf("\App\Poker\CommunityCardManager", $this->manager);
    }

    /**
     * Test the actionIsClosed methods.
     */

    public function testCCMMethods()
    {
        $board = $this->manager->getBoard();
        $this->assertEmpty($board);

        $cards = ["card", "card", "card"];
        $this->manager->register($cards);
        $board = $this->manager->getBoard();
        $count = $this->manager->cardsDealt();

        $this->assertCount(3, $board);
        $this->assertEquals(3, $count);

        $this->manager->resetBoard();

        $board = $this->manager->getBoard();
        $this->assertEmpty($board);
    }

}