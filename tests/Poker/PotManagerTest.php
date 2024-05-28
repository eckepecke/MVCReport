<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class BetManager.
 */
class PotManagerTest extends TestCase
{
    private PotManager $manager;
    private array $state;

    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->manager = new PotManager();
        $this->state = ["players" => [new Player(), new Player(), new Player()]];
        
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreatePotManager()
    {
        $this->assertInstanceOf("\App\Poker\PotManager", $this->manager);
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testPotMethids()
    {
        $players = $this->state["players"];
        $player1 = $players[0];
        $player2 = $players[1];
        $player3 = $players[2];

        $player1->bet(1000);
        $player2->fold();
        $player2->call(1000);

        $this->manager->addChipsToPot($this->state);

        $res = $this->manager->getPotSize();
        $exp = 2000;

        $this->assertEquals($exp, $res);

        $this->manager->resetPot();

        $res = $this->manager->getPotSize();
        $exp = 0;

        $this->assertEquals($exp, $res);

        $this->manager->chargeBlinds($this->state["players"]);
        $res = $this->manager->getPotSize();
        $exp = 0;

        $this->assertEquals($exp, $res);
    }
}