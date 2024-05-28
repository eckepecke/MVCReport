<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class StateManager.
 */
class StateManagerTest extends TestCase
{
    private StateManager $manager;
    private array $state;

    
    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->manager = new StateManager();
        $this->state = ["players" => [new Player(), new Player(), new Player()]];

    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateStateManager()
    {
        $this->assertInstanceOf("\App\Poker\StateManager", $this->manager);
    }

    /**
     * Check that active players are counted correctly.
     */
    public function testGetActivePlayers()
    {
        $res = $this->manager->getActivePlayers($this->state);
        $res = $this->manager->getActivePlayers($this->state);

        $exp = 3;
        $this->assertSame($exp, $res);

        $this->state["players"][0]->deactivate();

        $res = $this->manager->getActivePlayers($this->state);

        $exp = 2;
        $this->assertSame($exp, $res);
    }

    /**
     * Check that winner is found when only aone active player.
     */
    public function testFindWinner()
    {
        $this->state["players"][0]->deactivate();
        $this->state["players"][1]->deactivate();

        $winner = $this->manager->getWinner($this->state);

        $exp = $this->state["players"][2];
        $this->assertSame($exp, $winner);
    }


    /**
     * Check that active players are counted correctly.
     */
    public function testRemoveInactivePlayers()
    {

        $this->state["players"][0]->deactivate();

        $res = $this->manager->removeInactive($this->state["players"]);

        $exp = 2;
        $this->assertCount($exp, $res);
    }


    /**
     * Check that hero input is interpreted correctly.
     */
    public function testHeroAlreadyMoved()
    {

        $action1 = "next";
        $action2 = null;
        $action3 = "check";

        $res1 = $this->manager->heroAlreadyMoved($action1);
        $res2 = $this->manager->heroAlreadyMoved($action2);
        $res3 = $this->manager->heroAlreadyMoved($action3);

        $this->assertFalse($res1);
        $this->assertFalse($res2);
        $this->assertTrue($res3);
    }

    /**
     * Check that hero input is interpreted correctly.
     */
    public function testWonWithNoShowdown()
    {
        $bool = $this->manager->setNewHand(false);
        $bool = $this->manager->getNewHand();
        $this->assertFalse($bool);

        $this->state["players"][0]->deactivate();
        $this->state["players"][1]->deactivate();
        $this->manager->wonWithNoShowdown($this->state);

        $bool = $this->manager->getNewHand();
        $this->assertTrue($bool);
    }

}