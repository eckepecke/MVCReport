<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;
// use App\Poker\Hero;
/**
 * Test cases for class HeroActionManager.
 */
class HeroActionManagerTest extends TestCase
{
    private HeroActionManager $manager;
    private Hero $hero;
    
    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->manager = new HeroActionManager();
        $this->hero = new Hero();
        
    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateHeroActionManager()
    {
        $this->assertInstanceOf("\App\Poker\HeroActionManager", $this->manager);
    }

    /**
     * Test differnt Hero actions to they register.
     */
    public function testHeroActions()
    {
        $action = "call";
        $price = 100;
        $this->manager->heroMove($action, $this->hero, $price);
        $resAction = $this->hero->getLastAction();
        $exp = "call";

        $this->assertSame($exp, $resAction);

        $action = "check";
        $price = 0;
        $this->manager->heroMove($action, $this->hero, $price);
        $resAction = $this->hero->getLastAction();
        $exp = "check";

        $this->assertSame($exp, $resAction);

        $action = "50";
        $price = 0;
        $this->manager->heroMove($action, $this->hero, $price);
        $resAction = $this->hero->getLastAction();
        $exp = "bet";

        $this->assertSame($exp, $resAction);

        $action = "fold";
        $price = 0;
        $this->manager->heroMove($action, $this->hero, $price);
        $resAction = $this->hero->getLastAction();
        $exp = "fold";

        $this->assertSame($exp, $resAction);
    }
}