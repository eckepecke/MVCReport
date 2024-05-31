<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class BetManager.
 */
class BetManagerTest extends TestCase
{
    private BetManager $manager;
    private array $state;
    
    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->manager = new BetManager();
        $this->state = ["players" => [new Player(), new Player(), new Player()]];
        
    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateBetManager()
    {
        $this->assertInstanceOf("\App\Poker\BetManager", $this->manager);
    }

    /**
     * Test the actionIsClosed methods.
     */
    public function testActionIsClosed()
    {
        $res = $this->manager->getActionIsClosed();
        $this->assertFalse($res);

        $this->manager->setActionIsClosed(true);

        $res = $this->manager->getActionIsClosed();
        $this->assertTrue($res);
    }

    /**
     * Test the getPriceToPlay methods.
     */
    public function testGetPrice()
    {
        $players = $this->state["players"];
        $bettor = $players[0];
        $bettor->bet(1000);

        $res = $this->manager->getPriceToPlay($this->state);
        $exp = 1000;
        $this->assertSame($exp, $res);
    }

    /**
     * Test the getPriceToPlay methods.
     */
    public function testGetBiggestBet()
    {
        $players = $this->state["players"];
        $bettor1 = $players[0];
        $bettor2 = $players[1];
        $bettor1->bet(1000);
        $bettor2->bet(2000);

        $res = $this->manager->getBiggestBet($this->state);
        $exp = 2000;
        $this->assertSame($exp, $res);
    }

    /**
     * Test the getPriceToPlay methods.
     */
    public function testGetMinimumRaiseAllowed()
    {
        $players = $this->state["players"];
        $bettor1 = $players[0];
        $bettor1->bet(1000);

        $res = $this->manager->getMinimumRaiseAllowed($this->state);
        $exp = 2000;
        $this->assertSame($exp, $res);
    }

    /**
     * Test the reset methods.
     */
    public function testResetMethods()
    {
        $players = $this->state["players"];
        $bettor1 = $players[0];
        $bettor2 = $players[1];
        $bettor3 = $players[2];

        $bettor1->bet(1000);
        $bettor2->bet(1000);
        $bettor3->bet(99999);

        $this->manager->resetPlayerBets($players);
        $this->manager->resetPlayerActions($players);


        $expBet = 0;
        $expAction = "";

        foreach($players as $player) {
            $bet = $player->getCurrentBet();
            $action = $player->getLastAction();

            $this->assertSame($expBet, $bet);
            $this->assertSame($expAction, $action);

        }
    }


    /**
     * Test the close action method.
     */
    public function testPlayerClosedAction(): void
    {
        $this->state["active"] = 3;
        $this->state["street"] = "flop";
        $this->state["pot"] = 1000;


        $players = $this->state["players"];
        $player1 = $players[0];
        $player2 = $players[1];
        $player3 = $players[2];

        $player1->setPosition(0);
        $player2->setPosition(1);
        $player3->setPosition(2);

        $player1->bet(1000);
        $player2->fold();


        $res1 = $this->manager->playerClosedAction($player1, $this->state);
        $res2 = $this->manager->playerClosedAction($player2, $this->state);


        $this->assertFalse($res1);
        $this->assertFalse($res2);

        $player3->call(1000);
        $res3 = $this->manager->playerClosedAction($player3, $this->state);
        $this->assertTrue($res3);

        $this->manager->resetPlayerBets($this->state["players"]);
        $this->manager->resetPlayerActions($this->state["players"]);

        $player1->check();
        $res1 = $this->manager->playerClosedAction($player1, $this->state);

        $player2->check();
        $res2 = $this->manager->playerClosedAction($player2, $this->state);

        $player3->check();
        $res3 = $this->manager->playerClosedAction($player3, $this->state);

        $this->assertFalse($res1);
        $this->assertFalse($res2);
        $this->assertTrue($res3);

        $player1->bet(1000);
        $player2->fold();
        $player3->raise(3000);

        $res3 = $this->manager->playerClosedAction($player3, $this->state);
        $this->assertFalse($res3);
    }
}