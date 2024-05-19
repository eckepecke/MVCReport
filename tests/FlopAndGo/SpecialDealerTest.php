<?php

namespace App\FlopAndGo;

use PHPUnit\Framework\TestCase;
use App\Cards\DeckOfCards;

/**
 * Test cases for class SpecialDealer.
 */
class SpecialDealerTest extends TestCase
{
    /**
     * @var SpecialDealer
     */
    private $dealer;
    private $player1;
    private $player2;
    private $deck;
    private $table;

    /**
     * Set up the test environment before each test method.
     */
    public function setUp(): void
    {
        $this->dealer = new SpecialDealer();
        $this->player1 = new Hero();
        $this->player2 = new Villain();
        $this->dealer->getPlayerList([$this->player1, $this->player2]);
        $this->deck = new DeckOfCards();
        $this->dealer->addDeck($this->deck);
        $this->table = new SpecialTable();
        $this->dealer->addTable($this->table);
    }


    /**
     * Remove properties so no weird stuff happens.
     */
    public function tearDown(): void
    {
    $this->dealer = null;
    $this->player1 = null;
    $this->player2 = null;
    $this->deck = null;
    $this->table = null;
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateDealer()
    {
        $this->assertInstanceOf("\App\FlopAndGo\SpecialDealer", $this->dealer);
    }

    /**
     * Check that 4 cards left the deck and went to players after dealing.
     */
    public function testDealHoleCards()
    {
        $this->dealer->dealHoleCards();
        $cardsRemaining = $this->deck->size();
        $exp = 48;
        $this->assertEquals($exp, $cardsRemaining);

        $hand1 = $this->player1->getHand();
        $hand2 = $this->player2->getHand();

        $this->assertCount(2, $hand1);
        $this->assertCount(2, $hand2);
    }


    /**
     * Test to see that "all in check" is working as expected.
     */
    public function testPlayersAllIn()
    {
        $res = $this->dealer->playersAllIn();
        $exp = false;

        $this->assertSame($exp, $res);

        $this->player1->bet(5000);

        $res = $this->dealer->playersAllIn();
        $exp = true;

        $this->assertSame($exp, $res);
    }

    /**
     * Test to see that dealer takes the cards back and empties the table.
     */
    public function testResetForNextHand()
    {
        // Some action
        $this->dealer->dealHoleCards();
        $this->dealer->dealFlop();
        $bet = 1000;
        $this->player1->bet($bet);
        $this->player2->call($bet);
        $this->table->addChipsToPot($bet + $bet);
        $this->player1->fold();

        $this->dealer->resetForNextHand();
        
        $player1Hand = $this->player1->getHand();
        $player2Hand = $this->player2->getHand();
        $potSize = $this->table->getPotSize();

        $this->assertEmpty($player1Hand);
        $this->assertEmpty($player2Hand);
        $this->assertSame(0, $potSize);
    }

    /**
     * Test to see that dealer all the communtiy cards.
     */
    public function testDealToShowdown()
    {
        $board = $this->table->getBoard();
        $this->assertEmpty($board);

        $this->dealer->dealToShowdown();
        $board = $this->table->getBoard();

        $this->assertCount(5, $board);
    }

    /**
     * Test to see that dealer moves chips to winner.
     */
    public function testMoveChips()
    {
        $player1Bet = 1000;
        $player2Bet = 3000;
        $this->player1->bet($player1Bet);
        $this->player2->bet($player2Bet);
        // $this->table->addChipsToPot($heroBet);
        // $this->table->addChipsToPot($villainBet);
        $this->dealer->moveChipsAfterFold();
        $resStackP1 = $this->player1->getStack();
        $expStackP1 = 4000;
        $resStackP2 = $this->player2->getStack();
        $expStackP2 = 6000;

        $this->assertSame($expStackP2, $resStackP2);
        $this->assertSame($expStackP1, $resStackP1);

    }

        /**
     * Test if return flop works.
     */
    public function testGetFlop()
    {
        $res = $this->table->getFlop();
        $exp = [];

        $this->assertSame($exp, $res);

        $flop = $this->dealer->dealFlop();
        $this->table->registerMany($flop);

        $res = $this->table->getFlop();
        $exp = 3;

        $this->assertCount($exp, $res);
    }

}