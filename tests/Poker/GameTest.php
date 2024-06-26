<?php

namespace App\Poker;

use PHPUnit\Framework\TestCase;
use App\Cards\DeckOfCards;


/**
 * Test cases for class game.
 */
class GameTest extends TestCase
{

    private Game $game;

    protected function setUp(): void
    {
        $this->game = new Game();
        $this->game->init();

        // $player1 = new Hero();
        // $player2 = new Player();
        // $player3 = new Player();

        // $dealer = new CardManager();
        // $deck = new DeckOfCards();
        // $dealer->addDeck($deck);

        // $playerArray = [$player1, $player2, $player3];
        // $this->game->addPlayers($playerArray);
    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateGame()
    {
        $this->assertInstanceOf("\App\Poker\Game", $this->game);
    }
    /**
     * Get players test.
     */
    public function testGetPlayers()
    {

        $res = $this->game->getPlayers();

        $this->assertInstanceOf("\App\Poker\Player", $res[0]);
        $this->assertInstanceOf("\App\Poker\Player", $res[1]);
        $this->assertInstanceOf("\App\Poker\Player", $res[2]);
    }

    // /**
    //  * Test that we reach showdown when hero allin.
    //  */
    // public function testReachShowdownWhenAllIn()
    // {
    //     $this->game->prepare(5000);
    //     $data = $this->game->getTemplateData();
    //     $showdown = $data["showdown"];
    //     $this->assertTrue($showdown);
    // }
}
