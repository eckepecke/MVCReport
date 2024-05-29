<?php

namespace App\Poker;


use PHPUnit\Framework\TestCase;
use App\Cards\DeckOfCards;
use App\Poker\CardHand;
use App\Poker\Manager;
use App\Poker\CommunityCardManager;
use App\Poker\PotManager;
use App\Poker\PositionManager;
use App\Poker\CardManager;
use App\Poker\BetManager;
use App\Poker\StreetManager;
use App\Poker\HeroActionManager;
use App\Poker\OpponentActionManager;
use App\Poker\StateManager;
use App\Poker\ShowdownManager;
use App\Poker\HandEvaluator;

use App\Poker\SameHandEvaluator;
use App\Poker\GameOverTracker;
use App\Cards\CardGraphic;


/**
 * Test cases for class Player.
 */
class ManagerTest extends TestCase
{

    /**
     * @var Manager
     */
    private $manager;
    /**
     * @var array
     */
    private $players;
    /**
     * @var array
     */
    private $state;

        /**
     * Set up the test environment before each test method.
     */
    public function setUp(): void
    {
        $this->manager = new Manager();
        $player1 = new Hero();
        $player1->setName("Hero");
        // $this->hero = $player1;
        $player2 = new Opponent();
        $player2->setName("Isildur1");
        $player3 = new Opponent();
        $player3->setName("Phil");
        $this->players = [
            $player1,
            $player2,
            $player3,
        ];



        $deck = new DeckOfCards();
        $manager = new Manager();
        $cCManager = new CommunityCardManager();
        $potManager = new PotManager(1000);
        $positionManager = new PositionManager();
        $cardManager = new CardManager();
        $betManager = new BetManager();
        $streetManager = new StreetManager();
        $heroActionManager = new HeroActionManager();
        $opponentActionManager = new OpponentActionManager();
        $stateManager = new StateManager();
        $showdownManager = new ShowdownManager();
        $handEvaluator = new HandEvaluator();
        $sameHandEvaluator = new SameHandEvaluator();
        $gameOverTracker = new GameOverTracker(1);
    
        $showdownManager->add($sameHandEvaluator);
        $positionManager->assignPositions($this->players);
    
        // This is extended dealer class
        $cardManager->addDeck($deck);
        $cardManager->addEvaluator($handEvaluator);
    
        $this->manager->addManager('CCManager', $cCManager);
        $this->manager->addManager('potManager', $potManager);
        $this->manager->addManager('positionManager', $positionManager);
        $this->manager->addManager('cardManager', $cardManager);
        $this->manager->addManager('betManager', $betManager);
        $this->manager->addManager('streetManager', $streetManager);
        $this->manager->addManager('heroActionManager', $heroActionManager);
        $this->manager->addManager('opponentActionManager', $opponentActionManager);
        $this->manager->addManager('stateManager', $stateManager);
        $this->manager->addManager('showdownManager', $showdownManager);
        $this->manager->addManager('gameOverTracker', $gameOverTracker);
    
        $this->state = [
            "players" => $this->players,
            "active" => $this->manager->access("stateManager")->removeInactive($this->players),
            "board" => $this->manager->access("CCManager")->getBoard()
            ];
    
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateManager()
    {
        $this->assertInstanceOf("\App\Poker\Manager", $this->manager);
    }

    /**
     * Test that pot is given to the only active player.
     */
    public function testGivePotToWinner()
    {
        $initialStack = $this->players[2]->getStack();
        $exp = 10000;
        $this->assertEquals($exp, $initialStack);
        // Deactivate all but one player.

        $this->players[1]->deactivate();
        $this->players[0]->deactivate();
        $state["players"] = $this->players;

        $this->manager->givePotToWinner($state);

        $newStack = $this->players[2]->getStack();
        $newExp = 11000;
        $this->assertNotEquals($newExp, $initialStack);
        $this->assertEquals($newExp, $newStack);
    }

    /**
     * Test showdownwinner takes pot.
     */
    public function testShowdown(): void
    {
        $initialStack = $this->players[2]->getStack();
        $exp = 10000;
        $this->assertEquals($exp, $initialStack);
        $card1 = $this->createMock(CardGraphic::class);
        $card1->method('getValue')
            ->willReturn('ace');
        $card1->method('getSuit')
            ->willReturn('whatever');
        $cardStubs = [$card1, $card1];

        $winningHand = $this->createMock(CardHand::class);
        $winningHand->method('getCardArray')
            ->willReturn($cardStubs);
        $winningHand->method('getStrengthInt')
            ->willReturn(8);

        $losingHand = $this->createMock(CardHand::class);
        $losingHand->method('getCardArray')
            ->willReturn($cardStubs);
        $losingHand->method('getStrengthInt')
            ->willReturn(4);

        $this->players[0]->addHand($losingHand);
        $this->players[1]->addHand($losingHand);
        $this->players[2]->addHand($winningHand);

        $board = [];

        $state["players"] = $this->players;
        $state["board"] = $board;
        $state["active"] = $this->players;

        $this->manager->showdown($state);

        $resStack = $this->players[2]->getStack();
        $expStack = 11000;

        $this->assertEquals($expStack, $resStack);
    }

    /**
     * Test deal functions deal next street and resets variables.
     */
    public function testDeal(): void
    {
        $initialStreet = $this->manager->access("streetManager")->getStreet();
        $initialPot = $this->manager->access("potManager")->getPotSize();
        $initialCards = $this->manager->access("CCManager")->cardsDealt();

        foreach ($this->state["players"] as $player) {
            $player->bet(50);
        }


        $this->manager->deal($this->state);
        $nextStreet = $this->manager->access("streetManager")->getStreet();
        $afterPot = $this->manager->access("potManager")->getPotSize();
        $afterCards = $this->manager->access("CCManager")->cardsDealt();

        $this->assertNotSame($initialStreet, $nextStreet);
        $this->assertNotSame($initialPot, $afterPot);
        $this->assertEquals(1150, $afterPot);
        $this->assertNotSame($initialCards, $afterCards);




    }
}