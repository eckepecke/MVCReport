<?php

namespace App\FlopAndGo;

use PHPUnit\Framework\TestCase;
use App\Cards\DeckOfCards;
use App\Cards\CardGraphic;


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
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateDealer()
    {
        $this->assertInstanceOf("\App\FlopAndGo\Dealer", $this->dealer);
    }

    /**
    * Try dealing Cards.
     */
    public function testDealingAndShuffling()
    {
        $card = $this->dealer->dealOne();
        $res = $this->deck->size();
        $exp = 51;

        $this->assertSame($res, $exp);
        $this->assertInstanceOf("\App\Cards\CardGraphic", $card);

        
        $this->dealer->shuffleCards();
        $res = $this->deck->size();
        $exp = 52;

        $this->assertSame($res, $exp);

        $firstBoard = $this->dealer->dealRemaining([]);
        $this->dealer->shuffleCards();
        $secondBoard = $this->dealer->dealRemaining([]);

        $this->assertNotEquals($firstBoard, $secondBoard);
        $this->assertCount(5, $firstBoard);
        $this->assertCount(5, $secondBoard);
    }

}