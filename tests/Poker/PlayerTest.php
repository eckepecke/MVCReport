<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;
use App\Cards\DeckOfCards;
use App\Poker\CardHand;
use App\Cards\CardGraphic;




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
    public function testCreatePlayer()
    {
        $this->assertInstanceOf("\App\Poker\Player", $this->player);
    }

    /**
     * Give Player a CardHand object
     */
    public function testReceiveCards()
    {
        $hand = new CardHand();
        $hand->add(new CardGraphic());
        $hand->add(new CardGraphic());

        $this->player->addHand($hand);
        $hand = $this->player->getHand();
        $cards = $hand->getCardArray();

        $this->assertInstanceOf("\App\Poker\CardHand", $hand);
        $this->assertInstanceOf("\App\Cards\CardGraphic", $cards[0]);
        $this->assertInstanceOf("\App\Cards\CardGraphic", $cards[1]);
    }
}