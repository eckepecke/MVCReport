<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;
use App\Cards\DeckOfCards;
use App\Cards\CardGraphic;
use App\Cards\CardHand;

use App\Poker\Player;



/**
 * Test cases for class Hero.
 */
class DealerTest extends TestCase
{
    private $dealer;
    private $deck;


    /**
     * Set up the test environment before each test method.
     */
    public function setUp(): void
    {
        $this->dealer = new Dealer();
        $this->deck = new DeckOfCards();


        $this->dealer->addDeck($this->deck);
    }

    /**
     * Deal hole cards
     */
    public function testDealHoleCards(): void
    {
        $hand = $this->dealer->dealStartHand();

        $this->assertInstanceOf("\App\Cards\CardGraphic", $hand[0]);
        $this->assertInstanceOf("\App\Cards\CardGraphic", $hand[1]);
    }


}