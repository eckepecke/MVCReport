<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class StreetManager.
 */
class StreetManagerTest extends TestCase
{
    private StreetManager $manager;
    
    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->manager = new StreetManager();
    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateStreetanager()
    {
        $this->assertInstanceOf("\App\Poker\StreetManager", $this->manager);
    }

    /**
     * Assert next street is set correctly.
     */
    public function testSetNextStreet()
    {
        $before = $this->manager->getStreet();

        for ($i = 0; $i < 4; $i++) {
            $this->manager->setNextStreet();
        }

        $after = $this->manager->getStreet();
        $this->assertSame($before, $after);

        for ($i = 0; $i < 3; $i++) {
            $this->manager->setNextStreet();
        }

        $showdown = $this->manager->getShowdown();
        $this->assertTrue($showdown);

        $this->manager->resetStreet();
        $reset = $this->manager->getStreet();
        $this->assertSame($before, $reset);


        $this->manager->setShowdownFalse();
        $showdown = $this->manager->getShowdown();
        $this->assertFalse($showdown);

        $this->manager->setShowdownTrue();
        $showdown = $this->manager->getShowdown();
        $this->assertTrue($showdown);
    }

}