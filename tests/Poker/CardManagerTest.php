<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;
use App\Cards\CardHand;
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

        // $player1 = new Player();
        // $player2 = new Player();
        // $player3 = new Player();

        // $playerArray = [$player1, $player2, $player3];

        $this->manager->dealStartingHands($this->players);

        $hand1 = $playerArray[0]->getHand();
        $hand2 = $playerArray[1]->getHand();
        $hand3 = $playerArray[2]->getHand();

        $this->assertInstanceOf("\App\Poker\CardHand", $hand1);
        $this->assertInstanceOf("\App\Poker\CardHand", $hand2);
        $this->assertInstanceOf("\App\Poker\CardHand", $hand3);
    }

    /**
     * Give cards to everyone.
     */
    // public function testDealCardsToAllPlayers(): void
    // {

    //     // $player1 = new Player();
    //     // $player2 = new Player();
    //     // $player3 = new Player();

    //     // $playerArray = [$player1, $player2, $player3];

    //     $this->manager->dealStartingHands($this->players);

    //     $hand1 = $playerArray[0]->getHand();
    //     $hand2 = $playerArray[1]->getHand();
    //     $hand3 = $playerArray[2]->getHand();

    //     $this->assertInstanceOf("\App\Poker\CardHand", $hand1);
    //     $this->assertInstanceOf("\App\Poker\CardHand", $hand2);
    //     $this->assertInstanceOf("\App\Poker\CardHand", $hand3);
    // }
}