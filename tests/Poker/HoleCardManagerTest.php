<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;
use App\Cards\CardHand;
use App\Cards\DeckOfCards;



/**
 * Test cases for class HoleCardManager.
 */
class HoleCardManagerTest extends TestCase
{
    private HoleCardManager $manager;
    
    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->manager = new HoleCardManager();


        $deck = new DeckOfCards();
        $this->manager->addDeck($deck);


    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateHoleCardManager()
    {
        $this->assertInstanceOf("\App\Poker\HoleCardManager", $this->manager);
    }


    /**
     * Give cards to everyone.
     */
    public function testDealCardsToAllPlayers(): void
    {

        $player1 = new Player();
        $player2 = new Player();
        $player3 = new Player();

        $playerArray = [$player1, $player2, $player3];

        $this->manager->dealStartHandToAllPlayers($playerArray);

        $hand1 = $playerArray[0]->getHand();
        $hand2 = $playerArray[1]->getHand();
        $hand3 = $playerArray[2]->getHand();

        $this->assertInstanceOf("\App\Cards\CardHand", $hand1);
        $this->assertInstanceOf("\App\Cards\CardHand", $hand2);
        $this->assertInstanceOf("\App\Cards\CardHand", $hand3);
    }
}