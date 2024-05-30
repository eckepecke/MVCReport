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
        // $this->state["hero"] = $player1;
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
            "board" => $this->manager->access("CCManager")->getBoard(),
            "hero" => $player1,
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
        $exp = 50000;
        $this->assertEquals($exp, $initialStack);
        // Deactivate all but one player.

        $this->players[1]->deactivate();
        $this->players[0]->deactivate();
        $state = [];
        $state["players"] = $this->players;

        $this->manager->givePotToWinner($state);

        $newStack = $this->players[2]->getStack();
        $newExp = 51000;
        $this->assertNotEquals($newExp, $initialStack);
        $this->assertEquals($newExp, $newStack);
    }

    /**
     * Test showdownwinner takes pot.
     */
    public function testShowdown(): void
    {
        $initialStack = $this->players[2]->getStack();
        $exp = 50000;
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
        $state = [];
        $state["players"] = $this->players;
        $state["board"] = $board;
        $state["active"] = $this->players;

        $this->manager->showdown($state);

        $resStack = $this->players[2]->getStack();
        $expStack = 51000;

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

    /**
     * Test that opponents move in correct order.
     */
    public function testOpponentsMove(): void
    {
        $heroPos = $this->state["hero"]->getPosition();
        $this->assertEquals(0,$heroPos);

        $firstOpponent = $this->state["players"][1];
        $secondOpponent = $this->state["players"][2];

        $actionBefore1 = $firstOpponent->getLastAction();
        $actionBefore2 = $secondOpponent->getLastAction();

        $this->manager->opponentsInFrontMove($this->state);

        $action1 = $firstOpponent->getLastAction();
        $action2 = $secondOpponent->getLastAction();
        // Since hero has position zero we expect action not to update.
        $initial = "";
        $this->assertEquals($initial, $actionBefore1);
        $this->assertEquals($initial, $actionBefore1);
        $this->assertEquals($initial, $action1);
        $this->assertEquals($initial, $action2);

        $this->manager->opponentsBehindMove($this->state);

        // Now opponents should have updated moves.
        $action1 = $firstOpponent->getLastAction();
        $action2 = $secondOpponent->getLastAction();
        $this->assertNotSame($initial, $action1);
        $this->assertNotSame($initial, $action2);

        $this->manager->access("positionManager")->updatePositions($this->state["players"]);
        $this->manager->access("betManager")->resetPlayerActions($this->state["players"]);

        // Now Hero is last to act.
        $heroPos = $this->state["hero"]->getPosition();
        $this->assertEquals(2,$heroPos);

        $actionBefore1 = $firstOpponent->getLastAction();
        $actionBefore2 = $secondOpponent->getLastAction();

        $this->manager->opponentsBehindMove($this->state);

        $action1 = $firstOpponent->getLastAction();
        $action2 = $secondOpponent->getLastAction();
        // Since hero has position 2 we expect action not to update
        // when behind players move (there are none).
        $initial = "";
        $this->assertEquals($initial, $actionBefore1);
        $this->assertEquals($initial, $actionBefore1);
        $this->assertEquals($initial, $action1);
        $this->assertEquals($initial, $action2);

        $this->manager->opponentsInFrontMove($this->state);

        // Now opponents should have updated moves.
        $action1 = $firstOpponent->getLastAction();
        $action2 = $secondOpponent->getLastAction();
        $this->assertNotSame($initial, $action1);
        $this->assertNotSame($initial, $action2);
    }

    /**
     * Test that both opponents move when hero is allin.
     */
    public function testOpponentsPlayVAllIn(): void
    {
        // Put hero allin.
        $this->state["hero"]->bet(5000);
        $firstOpponent = $this->state["players"][1];
        $secondOpponent = $this->state["players"][2];

        $actionBefore1 = $firstOpponent->getLastAction();
        $actionBefore2 = $secondOpponent->getLastAction();

        $this->manager->opponentsPlay("5000", $this->state);

        $action1 = $firstOpponent->getLastAction();
        $action2 = $secondOpponent->getLastAction();
        $initial = "";
        // Both players should have made a move.
        $this->assertEquals($initial, $actionBefore1);
        $this->assertEquals($initial, $actionBefore1);
        $this->assertNotSame($initial, $action1);
        $this->assertNotSame($initial, $action2);
    }

    /**
     * Test that only opponents infront move when hero closes action.
     */
    public function testOpponentsInFrontPlayVsActionClose(): void
    {

        // $this->state["hero"]->call(1);
        $copyOfState = $this->state;
        $firstOpponent = $this->state["players"][1];
        $secondOpponent = $this->state["players"][2];

        $actionBefore1 = $firstOpponent->getLastAction();
        $actionBefore2 = $secondOpponent->getLastAction();
        $this->manager->access("stateManager")->setNewHand(false);
        $this->manager->access("betManager")->setActionIsClosed(true);

        $this->manager->opponentsPlay("call", $this->state);

        $action1 = $firstOpponent->getLastAction();
        $action2 = $secondOpponent->getLastAction();
        $initial = "";
        // Nobody should move since hero has position 0.
        $this->assertEquals($initial, $actionBefore1);
        $this->assertEquals($initial, $actionBefore1);
        $this->assertSame($initial, $action1);
        $this->assertSame($initial, $action2);

        $this->manager->access("betManager")->setActionIsClosed(false);
        var_dump($this->manager->access("betManager")->getActionIsClosed());
        $this->manager->opponentsPlay("500", $copyOfState);

        $action1 = $firstOpponent->getLastAction();
        $action2 = $secondOpponent->getLastAction();
        $initial = "";
        // Now both opponents should move since
        // action is not closed and hero acts first.
        $this->assertNotSame($initial, $action1);
        $this->assertNotSame($initial, $action2);
    }


    /**
     * Test that different hero actions register correctly.
     */
    public function testHeroMakesAPlay() {
        $hero = $this->state["players"][0];

        $this->manager->heroMakesAPlay("observe", $this->state);

        $action = $hero->getLastAction();
        $exp = "";
        $this->assertSame($exp, $action);

        $this->manager->heroMakesAPlay(null, $this->state);

        $action = $hero->getLastAction();
        $exp = "";
        $this->assertSame($exp, $action);

        $this->manager->heroMakesAPlay("1337", $this->state);

        $action = $hero->getLastAction();
        $exp = "bet";
        $this->assertSame($exp, $action);

        $this->manager->heroMakesAPlay("check", $this->state);

        $action = $hero->getLastAction();
        $exp = "check";
        $this->assertSame($exp, $action);

        $this->manager->heroMakesAPlay("fold", $this->state);

        $action = $hero->getLastAction();
        $exp = "fold";
        $this->assertSame($exp, $action);
    }

    /**
     * Test that all remaining cards are dealt when calling dealToShowDown.
     */
    public function testDealToShowDown() {

        $board = $this->manager->access("CCManager")->getBoard();
        $this->assertCount(0, $board);

        $this->manager->dealToShowDown();
        $board = $this->manager->access("CCManager")->getBoard();
        $this->assertCount(5, $board);
    }

    /**
     * Test that resetTable resets properties.
     */
    public function testResetTable() {
        $this->manager->dealToShowDown();
        foreach ($this->players as $player) {
            $player->bet(1000);
        }
        $this->manager->access("potManager")->addChipsToPot($this->state);

        $pot = $this->manager->access("potManager")->getPotSize();
        $board = $this->manager->access("CCManager")->getBoard();

        $expPot = 4000;
        $this->assertSame($expPot, $pot);
        $this->assertCount(5, $board);

        $this->manager->resetTable($this->state["players"]);

        $pot = $this->manager->access("potManager")->getPotSize();
        $board = $this->manager->access("CCManager")->getBoard();

        $expPot = 0;
        $this->assertSame($expPot, $pot);
        $this->assertEmpty($board);


    }
}