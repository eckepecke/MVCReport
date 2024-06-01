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
        $this->manager = new PotManager(0);
        $this->state = ["players" => [new Hero(), new Player(), new Player()]];
        
        
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
     * Test pot methods.
     */
    public function testPotMethods()
    {
        $players = $this->state["players"];
        $player1 = $players[0];
        $player2 = $players[1];

        $player1->bet(1000);
        $player2->fold();
        $player2->call(1000);
        $this->state["hero"] = $player1;
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

    /**
     * Test adding chips to the pot when the hero is not all-in.
     */
    public function testAddChipsToPotHeroNotAllIn()
    {
        $players = $this->state["players"];
        $player1 = $players[0];
        $player2 = $players[1];
        $player3 = $players[2];

        // Simulate bets
        $player1->bet(1000);
        $player2->bet(1500);
        $player3->bet(2000);

        $this->state["hero"] = $player1;
        $this->manager->addChipsToPot($this->state);

        $res = $this->manager->getPotSize();
        $exp = 4500;

        $this->assertEquals($exp, $res);
    }

    /**
     * Test adding chips to the pot when the hero is all-in.
     */
    public function testAddChipsToPotHeroAllIn()
    {
        $players = $this->state["players"];
        $hero = $players[0];
        $player2 = $players[1];
        $player3 = $players[2];
        $player3->deactivate();

        // player shoves allin
        $player2->bet(5000);

        // Hero calls all his chips
        $hero->call(2000);
        $this->state["hero"] = $hero;

        $this->manager->addChipsToPot($this->state);

        // Player2 bet should be adjusted to match the hero's bet

        $res = $this->manager->getPotSize();
        $exp = 4000;

        $this->assertEquals($exp, $res);
    }

    /**
     * Test resetting the pot.
     */
    public function testResetPot()
    {
        // Simulate some bets
        $players = $this->state["players"];
        $player1 = $players[0];
        $player1->bet(1000);

        $this->state["hero"] = $player1;
        $this->manager->addChipsToPot($this->state);

        $this->manager->resetPot();

        $res = $this->manager->getPotSize();
        $exp = 0;

        $this->assertEquals($exp, $res);
    }

    /**
     * Test charging blinds (example, not implemented in provided code).
     */
    public function testChargeBlinds()
    {
        $players = $this->state["players"];
        $players[0]->setPosition(0);
        $players[1]->setPosition(1);
        $players[2]->setPosition(2);

        $this->manager->chargeBlinds($players);
        $sb = $players[0]->getCurrentBet();
        $bb = $players[1]->getCurrentBet();
        $btn = $players[2]->getCurrentBet();

        $expSb = 100;
        $expBb = 200;
        $expBtn = 400;


        $this->assertEquals($expSb, $sb);
        $this->assertEquals($expBb, $bb);
        $this->assertEquals($expBtn, $btn);

    }
}