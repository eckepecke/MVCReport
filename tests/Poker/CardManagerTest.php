<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;
use App\Poker\CardHand;
use App\Cards\CardGraphic;
use App\Cards\DeckOfCards;



/**
 * Test cases for class HoleCardManager.
 */
class CardManagerTest extends TestCase
{
    private CardManager $manager;
    private array $players;
    
    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->manager = new CardManager();
        $deck = new DeckOfCards();
        $this->manager->addDeck($deck);

        $player1 = new Player();
        $player2 = new Player();
        $player3 = new Player();

        $playerArray = [$player1, $player2, $player3];
        $this->players = $playerArray;
    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateCardManager()
    {
        $this->assertInstanceOf("\App\Poker\CardManager", $this->manager);
    }


    /**
     * Give cards to everyone.
     */
    public function testDealCardsToAllPlayers(): void
    {
        $this->manager->dealStartingHands($this->players);

        $hand1 = $this->players[0]->getHand();
        $hand2 = $this->players[1]->getHand();
        $hand3 = $this->players[2]->getHand();

        $this->assertInstanceOf("\App\Poker\CardHand", $hand1);
        $this->assertInstanceOf("\App\Poker\CardHand", $hand2);
        $this->assertInstanceOf("\App\Poker\CardHand", $hand3);
    }

    /**
     * Give cards to everyone.
     */
    public function testDealCommunityCards(): void
    {
        // Flop
        $flopCards = $this->manager->dealCommunityCards("flop", 0);
        $this->assertCount(3, $flopCards);
        foreach ($flopCards as $card) {
            $this->assertInstanceOf("\App\Cards\Card", $card);
        }

        // Turn
        $turnCard = $this->manager->dealCommunityCards("turn", 3);
        $this->assertCount(1, $turnCard);
        $this->assertInstanceOf("\App\Cards\Card", $turnCard[0]);

        // River
        $riverCard = $this->manager->dealCommunityCards("river", 4);
        $this->assertCount(1, $riverCard);
        $this->assertInstanceOf("\App\Cards\Card", $riverCard[0]);
    }

    /**
     * UpdateHand strength.
     */
    public function testUpdateHandStrengths(): void
    {
        $evaluator = new HandEvaluator();
        $this->manager->addEvaluator($evaluator);

        $card1 = new CardGraphic();
        $card2 = new CardGraphic();
        $card3 = new CardGraphic();


        $card1->setSuit('hearts');
        $card2->setSuit('spades');
        $card3->setSuit('clubs');

        $card1->setValue(2);
        $card2->setValue(3);
        $card3->setValue(3);

        $hand = new CardHand();
        $hand->add($card1);
        $hand->add($card2);

        foreach($this->players as $player) {
            $player->addHand($hand);
        }

        $board = [];

        $this->manager->updateHandStrengths($this->players, $board);

        $exp = "High card";
        foreach($this->players as $player) {
            $hand = $player->getHand();
            $this->assertSame($exp, $hand->getStrengthString());
        }

        $board = [$card3];
        $this->manager->updateHandStrengths($this->players, $board);

        $exp = "One pair";
        foreach($this->players as $player) {
            $hand = $player->getHand();
            $this->assertSame($exp, $hand->getStrengthString());
        }
    }

    /**
     * Deal to river test.
     */
    public function testDealRemaining(): void
    {
        $board1 = [];
        $board2 = ["card"];
        $board3 = ["card", "card"];
        $board4 = ["card", "card","card"];
        $board5 = ["card","card","card","card"];
        $board6 = ["card","card","card","card","card"];

        $res1 = $this->manager->dealRemaining($board1);
        $res2 = $this->manager->dealRemaining($board2);
        $res3 = $this->manager->dealRemaining($board3);
        $res4 = $this->manager->dealRemaining($board4);
        $res5 = $this->manager->dealRemaining($board5);
        $res6 = $this->manager->dealRemaining($board6);

        $this->assertCount(5, $res1);
        $this->assertCount(4, $res2);
        $this->assertCount(3, $res3);
        $this->assertCount(2, $res4);
        $this->assertCount(1, $res5);
        $this->assertCount(0, $res6);
    }


    /**
     * Test reset methods.
     */
    public function testResetMethods(): void
    {
        foreach ($this->players as $player) {
            $player->deactivate();
            $res = $player->isActive();
            $this->assertFalse($res);
        }

        $this->manager->activatePlayers($this->players);

        foreach ($this->players as $player) {
            $res = $player->isActive();
            $this->assertTrue($res);
        }

        $card1 = new CardGraphic();
        $card2 = new CardGraphic();

        $hand = new CardHand();
        $hand->add($card1);
        $hand->add($card2);
        foreach ($this->players as $player) {
            $player->addHand($hand);
            $this->assertNotEmpty($player->getHand());
        }

        $this->manager->resetPlayerHands($this->players);
        foreach ($this->players as $player) {
            $this->assertEmpty($player->getHand());
        }
    }
}