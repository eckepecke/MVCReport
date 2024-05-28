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
    public function testCreateBetManager()
    {
        $this->assertInstanceOf("\App\Poker\CommunityCardManager", $this->manager);
    }

    /**
     * Test the actionIsClosed methods.
     */

}