<?php

namespace App\FlopAndGo;

use PHPUnit\Framework\TestCase;
use App\Cards\DeckOfCards;

/**
 * Test cases for class game.
 */
class GameTest extends TestCase
{
    private Game $game;
    private Hero $hero;
    private Villain $villain;
    private SpecialDealer $dealer;
    private SpecialTable $table;
    private HandChecker $handChecker;
    private Challenge $challenge;
    private DeckOfCards $deck;


    protected function setUp(): void
    {

        $this->game = new Game();
        $this->hero = new Hero();
        $this->villain = new Villain();
        $this->table = new SpecialTable();
        $this->dealer = new SpecialDealer();
        $this->deck = new DeckOfCards();
        $this->handChecker = new HandChecker();
        $this->challenge = new Challenge();

        $this->dealer->addDeck($this->deck);
        $this->dealer->addTable($this->table);
        $this->table->seatPlayers($this->hero, $this->villain);
        $this->game->addHero($this->hero);
        $this->game->addVillain($this->villain);
        $this->game->addTable($this->table);
        $this->game->addDealer($this->dealer);
        $this->game->addHandChecker($this->handChecker);
        $this->game->addChallenge($this->challenge);
        $this->dealer->getPlayerList([$this->hero, $this->villain]);
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateGame()
    {
        $this->assertInstanceOf("\App\FlopAndGo\Game", $this->game);
    }


    /**
     * Test that max bet is not more than players stack and current bet.
     */
    public function testMaxBet() {
        $res = $this->game->getMaxBet($this->hero, $this->villain);
        $exp = 5000;

        $this->assertSame($exp, $res);

        $this->hero->bet(1000);
        $this->hero->fold();
        $res = $this->game->getMaxBet($this->hero, $this->villain);
        $exp = 4000;

        $this->assertSame($exp, $res);

        $this->hero->bet(1000);
        $res = $this->game->getMaxBet($this->hero, $this->villain);
        $exp = 4000;

        $this->assertSame($exp, $res);
    }

    /**
     * Test showdown signal.
     */
    public function testShowdownCheck() 
    {
        $res = $this->game->isShowdown();
        $this->assertFalse($res);

        $this->table->setStreet(4);
        $res = $this->game->isShowdown();
        $this->assertTrue($res);
    }

    /**
     * Test "All hands played" signal.
     */
    public function testAlHandsPlayedCheck() 
    {
        $res = $this->game->isAllHandsPlayed();
        $this->assertFalse($res);

        $this->challenge->incrementHandsPlayed();
        $this->challenge->incrementHandsPlayed();

        $res = $this->game->isAllHandsPlayed();
        $this->assertTrue($res);
    }

    /**
     * Test if any player is out of chips.
     */
    public function testIsSomeoneBroke() 
    {
        $res = $this->game->isSomeoneBroke();
        $this->assertFalse($res);

        $this->hero->bet(5000);
        $this->hero->fold();

        $res = $this->game->isSomeoneBroke();
        $this->assertTrue($res);

        $this->game->play(null);

        $data = $this->game->getGameState();
        $this->assertEquals(true, $data["game_over"]);

    }

    /**
     * Test that user input lead to correct action.
     */
    public function testHeroBet() 
    {
        $villain = $this->createMock(Villain::class);
        $villain->method('actionFacingBet')
                ->willReturn("call");
        $this->game->addVillain($villain);
    
        $bet = "1000";
    
        $this->game->heroAction($bet);
        $res = $this->hero->getLastAction();
        $exp = "bet";

        $this->assertSame($exp, $res);

    }

    /**
     * Test that hero uses expected betsize.
     */
    public function testHeroBetSize() 
    {
        $maxBet = 5000;
        $userInput = 1000;
        $res = $this->game->heroBetSize($userInput, $maxBet);
        $exp = 1000;

        $this->assertSame($exp, $res);

        $maxBet = 500;
        $res = $this->game->heroBetSize($userInput, $maxBet);
        $exp = 500;

        $this->assertSame($exp, $res);
    }

    /**
     * Test that important gamestate variables are behaving as expected
     */
    public function testGameStateVariables() 
    {
    // Players should start the game with 5k each and no hands
    // We should not be at showdown, game over when game start
    // We should be at a new hand 
    $data = $this->game->getGameState();

    $this->assertEquals([], $data["teddy_hand"]);
    $this->assertEquals([], $data["mike_hand"]);
    $this->assertEquals(5000, $data["teddy_stack"]);
    $this->assertEquals(5000, $data["mike_stack"]);
    $this->assertEquals(false, $data["game_over"]);
    $this->assertEquals(true, $data["new_hand"]);
    $this->assertEquals(false, $data["is_showdown"]);

    // Check if players are dealt cards when game starts
    // Pot cant be split so both players will never have starting stack after/during first hand
    $this->game->play(null);
    $data = $this->game->getGameState();

    $this->assertCount(2, $data["teddy_hand"]);
    $this->assertCount(2, $data["mike_hand"]);
    $this->assertNotEquals(5000, $data["teddy_stack"]);
    $this->assertNotEquals(5000, $data["mike_stack"]);

    // Hero out of chips causes game over
    $this->hero->bet(10000);
    $this->hero->fold();
    $this->game->play(null);
    $data = $this->game->getGameState();

    $this->assertEquals(true, $data["game_over"]);
    }

    /**
     * Test that all Cards are dealt when player is allin.
     */
    public function testAllInCheck() 
    {
        $board = $this->table->getBoard();
        $this->assertCount(0, $board);
        $this->hero->bet(5000);
        $this->game->allInCheck($this->hero);
        $board = $this->table->getBoard();
        $this->assertCount(5, $board);
    }

    /**
     * Test that correct street is dealt.
     */
    public function testDealCorrectStreet() 
    {
        // No cards dealt at first
        $board = $this->table->getBoard();
        $this->assertCount(0, $board);

        // 3 cards should be dealt
        $this->game->dealCorrectStreet();
        $board = $this->table->getBoard();
        $this->assertCount(3, $board);

        // 1 card should be dealt at next street
        $this->table->setStreet(2);
        $this->game->dealCorrectStreet();
        $board = $this->table->getBoard();
        $this->assertCount(4, $board);

        // 1 card should be dealt at next street
        $this->table->setStreet(3);
        $this->game->dealCorrectStreet();
        $board = $this->table->getBoard();
        $this->assertCount(5, $board);
    }


    /**
     * Test that game variables update after hero action.
     */
    public function testHeroActions() 
    {
        $action = "check";

        $this->game->heroAction($action);
        $resAction = $this->hero->getLastAction();
        $expAction = "check";

        $this->assertSame($expAction, $resAction);

        $action = "call";

        $this->game->heroAction($action);
        $resAction = $this->hero->getLastAction();
        $expAction = "call";

        $this->assertSame($expAction, $resAction);

        $action = "fold";

        $this->game->heroAction($action);
        $resAction = $this->hero->getLastAction();
        $expAction = "fold";

        $this->assertSame($expAction, $resAction);
    }

    /**
     * Test that game variables update after hero action.
     */
    public function testNewHandVariable() 
    {
        $res = $this->game->isNewHand();
        $this->assertTrue($res);

        $this->game->play(null);

        $res = $this->game->isNewHand();
        $this->assertFalse($res);
    }
}