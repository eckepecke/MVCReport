<?php

namespace App\FlopAndGo;

use PHPUnit\Framework\TestCase;
use App\FlopAndGo\Managers\Manager;
use App\Cards\DeckOfCards;


/**
 * Test cases for trait VillainActionManager.
 */
class VillainActionManager extends TestCase
{
    private Game $game;
    private Hero $hero;
    private Villain $villain;
    private SpecialDealer $dealer;
    private SpecialTable $table;
    private HandChecker $handChecker;
    private Challenge $challenge;
    private DeckOfCards $deck;
    private Manager $manager;


    /**
     * Set up the test environment before each test method.
     */
    public function setUp(): void
    {
        $this->game = new Game();
        $this->hero = new Hero();
        $this->villain = new Villain();
        $this->table = new SpecialTable();
        $this->dealer = new SpecialDealer();
        $this->deck = new DeckOfCards();
        $this->handChecker = new HandChecker();
        $this->challenge = new Challenge();
        $this->manager = new Manager();


        $this->dealer->addDeck($this->deck);
        $this->dealer->addTable($this->table);
        $this->table->seatPlayers($this->hero, $this->villain);
        $this->game->addHero($this->hero);
        $this->game->addVillain($this->villain);
        $this->game->addTable($this->table);
        $this->game->addDealer($this->dealer);
        $this->game->addHandChecker($this->handChecker);
        $this->game->addChallenge($this->challenge);
        $this->game->addManager($this->manager);
        $this->manager->addGame($this->game);
        $this->manager->addGameProperties();
    }


    /**
     * Test that Villain doesnt make his move when it is heros turn to move.
     */
    public function testVillainWaitsHisTurnIP(): void
    {
        $this->villain->setPosition("SB");
        $lastAction = $this->villain->getLastAction();
        $exp = '';

        $this->assertSame($exp, $lastAction);

        $heroAction = "check";

        $this->manager->villainPlay($heroAction);
        $lastAction = $this->villain->getLastAction();
        $expectedActions = [
            "check",
            "bet",
        ];

        $this->assertContains($lastAction, $expectedActions);
    }

    /**
     * Test that Villain makes move when it is his turn IP.
     */
    public function testVillainMakesMoveOnHisTurnIP(): void
    {
        $heroAction = 'check';

        $this->villain->setPosition("SB");
        $this->manager->villainPlay($heroAction);
        $lastAction = $this->villain->getLastAction();
        $expectedActions = [
            "check",
            "bet",
        ];

        $this->assertContains($lastAction, $expectedActions);
    }

    /**
     * Test that Villain makes move when it is his turn OOP.
     */
    public function testVillainMakesMoveOnHisTurnOOP(): void
    {
        $heroAction = 'check';

        $this->villain->setPosition("BB");
        $this->manager->villainPlay($heroAction);
        $lastAction = $this->villain->getLastAction();
        $expectedActions = [
            "check",
            "bet",
        ];

        $this->assertContains($lastAction, $expectedActions);
    }

    /**
     * Test that villainFoldVBet() works as intended.
     */
    public function testVillainFoldOk(): void
    {
        $pot = 1000;
        $this->table->addChipsToPot($pot);
        $this->hero->bet(500);
        $this->manager->villainFoldVBet();

        $heroStack = $this->hero->getStack();
        $expStack = 6000;

        $newHand = $this->manager->newHandCheck();

        $this->assertEquals($expStack, $heroStack);
        $this->assertTrue($newHand);
    }

    /**
     * Test that villainRaisedVBet() works as intended.
     */
    public function testVillaiRaiseVsBetOk(): void
    {

        $this->hero->bet(5000);
        $this->manager->villainRaisedVBet(5000);
        $action = $this->villain->getLastAction();
        $expAction = "call";

        // Assert Villain only calls when hero is allin
        $this->assertEquals($expAction, $action);

    }

    /**
     * Test that villainRaisedVBet() works as intended.
     */
    public function testVillainCalledVsBetOk(): void
    {
        $beforeStreet = $this->table->getStreet();

        $this->manager->villainCallBet(2000);
        $action = $this->villain->getLastAction();
        $expAction = "call";
        $afterStreet = $this->table->getStreet();

        // Assert Villain only calls when hero is allin
        $this->assertEquals($expAction, $action);
        $this->assertNotEquals($beforeStreet, $afterStreet);
    }
}