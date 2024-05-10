<?php

namespace App\FlopAndGo;

use PHPUnit\Framework\TestCase;
use App\Cards\DeckOfCards;


/**
 * Test cases for class Player.
 */
class PlayerTest extends TestCase
{

    /**
     * @var Player
     */
    private $player;


    /**
     * Set up the test environment before each test method.
     */
    public function setUp(): void
    {
        $this->player = new Player();
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateDealer()
    {
        $this->assertInstanceOf("\App\FlopAndGo\Player", $this->player);
    }

    /**
    * Test that when player checked property is updated.
     */
    public function testCheckLastAction()
    {
        $this->player->check();
        $res = $this->player->getLastAction();
        $exp = "check";

        $this->assertSame($res, $exp);
    }

    /**
    * Test that player stack property updates when taking the pot.
     */
    public function testTakePot()
    {
        $this->player->takePot(1000);
        $res =$this->player->getStack();
        $exp = 6000;

        $this->assertSame($res, $exp);
    }


    /**
    * Test that player position property is set correctly.
     */
    public function testPosition()
    {
        $this->player->setPosition("SB");
        $res =$this->player->getPosition();
        $exp = "SB";

        $this->assertSame($res, $exp);
    }

    /**
    * Test that player currentBet property works as expected.
     */
    public function testCurrentBet()
    {
        $res = $this->player->getCurrentBet();
        $exp = 0;

        $this->assertSame($res, $exp);

        $this->player->bet(1000);
        $res = $this->player->getCurrentBet();
        $exp = 1000;

        $this->assertSame($res, $exp);

        $this->player->setCurrentBet(500);
        $res = $this->player->getCurrentBet();
        $exp = 500;

        $this->assertSame($res, $exp);

        $this->player->resetCurrentBet();
        $res = $this->player->getCurrentBet();
        $exp = 0;

        $this->assertSame($res, $exp);
    }

    /**
    * Test that player action property is reset.
     */
    public function testResetLastAction()
    {
        $this->player->bet(1);
        $this->player->resetLastAction();
        $res =$this->player->getLastAction();
        $exp = "";

        $this->assertSame($res, $exp);
    }

    /**
    * Test that player action allin is returning correct bool value.
     */
    public function testAllin()
    {

        $res = $this->player->isAllin();
        $this->assertFalse($res);

        $this->player->bet(1);
        $res = $this->player->isAllin();

        $this->assertFalse($res);

        $this->player->bet(5000);
        $res = $this->player->isAllin();
        $this->assertTrue($res);
    }

    /**
    * Test that player pays table ante correctly.
     */
    public function testPayAnte()
    {

        $this->player->payAnte(200);
        $res = $this->player->getStack();
        $exp = 4800;

        $this->assertSame($res, $exp);
    }

    /**
    * Test that image paths are retrieved correctly.
     */
    public function testGetImagePaths()
    {
        $deck = new DeckOfCards();
        $dealer = new SpecialDealer();
        $opponent = new Player();
        $dealer->addDeck($deck);
        $dealer->getPlayerList([$this->player, $opponent]);
        $dealer->dealHoleCards($deck);

        $res = $this->player->getImgPaths();
        $exp = [
        0 => 'diamonds_ace.svg',
        1 => 'diamonds_3.svg'
        ];

        $this->assertSame($res, $exp);
    }
}
