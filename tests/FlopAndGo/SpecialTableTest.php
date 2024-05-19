<?php

namespace App\FlopAndGo;

use PHPUnit\Framework\TestCase;
use App\FlopAndGo\Hero;
use App\FlopAndGo\Villain;
use App\Cards\DeckOfCards;

/**
 * Test cases for class SpecialTable.
 */
class SpecialTableTest extends TestCase
{
    /**
     * @var SpecialTable
     */
    private $table;
    private $hero;
    private $villain;


    /**
     * Set up the test environment before each test method.
     */
    public function setUp(): void
    {
        $this->table = new SpecialTable();
        $this->hero = new Hero();
        $this->villain = new Villain();
        $this->table->seatPlayers($this->hero, $this->villain);
    }

    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateSpecialTable()
    {
        $this->assertInstanceOf("\App\FlopAndGo\SpecialTable", $this->table);
    }

    /**
     * Move the button test.
     */
    public function testMoveButton()
    {
        $before1 = $this->hero->getPosition();
        $before2 = $this->villain->getPosition();

        
        $this->table->moveButton();

        $after1 = $this->hero->getPosition();
        $after2 = $this->villain->getPosition();

        $this->assertNotEquals($before1, $after1);
        $this->assertNotEquals($before2, $after2);
        $this->assertEquals($before1, $after2);
        $this->assertEquals($before2, $after1);
    }

    /**
     * Test players getter function.
     */
    public function testGetPlayers()
    {
        $sbPlayer = $this->table->getSbPlayer();
        $bbPlayer = $this->table->getBbPlayer();

        $res = $sbPlayer->getPosition();
        $exp = "SB";
        $this->assertSame($res, $exp);


        $res = $bbPlayer->getPosition();
        $exp = "BB";
        $this->assertSame($res, $exp);

        $this->assertInstanceOf("\App\FlopAndGo\Player", $sbPlayer);
        $this->assertInstanceOf("\App\FlopAndGo\Player", $bbPlayer);
        $this->assertNotSame($sbPlayer, $bbPlayer);
    }

    /**
     * Test if table tracks the price to play.
     */
    public function testGetPriceToPlay()
    {
        $res = $this->table->getPriceToPlay();
        $exp = 0;
        $this->assertSame($res, $exp);

        $this->hero->bet(1000);
        $res = $this->table->getPriceToPlay();
        $exp = 1000;
        $this->assertSame($res, $exp);

        $this->villain->bet(3000);
        $res = $this->table->getPriceToPlay();
        $exp = 2000;
        $this->assertSame($res, $exp);
    }

    /**
     * Test if table tracks minraise rule correctly.
     */
    public function testMinRaiseTracker()
    {
        $res = $this->table->getMinimumRaiseAllowed();
        $exp = 50;
        $this->assertSame($res, $exp);

        $this->hero->bet(1000);
        $res = $this->table->getMinimumRaiseAllowed();
        $exp = 2000;
        $this->assertSame($res, $exp);
    }

    /**
     * Test if table register board cards.
     */
    public function testBoardFunctionality()
    {
        $dealer = new Dealer();
        $deck = new DeckOfCards();
        $dealer->addDeck($deck);
        $res = $this->table->getBoard();

        $this->assertEmpty($res);

        $flop = $dealer->dealFlop();

        $this->table->registerMany($flop);

        $res = $this->table->getBoard();

        $this->assertCount(3, $res);
    }

    /**
     * Test if table collects bom pot chips cards.
     */
    public function testBombPotCollection()
    {
        $this->table->getBombPotChips();
        $res = $this->table->getPotSize();
        $exp = 400;

        $this->assertSame($exp, $res);
    }

    /**
     * Test if street is being set to one if set to 1 after incrementing on street 4.
     */
    public function testSetStreet()
    {
        $this->table->setStreet(4);
        $this->table->setStreet(5);

        $res = $this->table->getStreet();
        $exp = 1;

        $this->assertSame($exp, $res);
    }

    /**
     * Test players are seated at different positions.
     */
    public function testSeatPlayers()
    {
        $this->table->seatPlayers($this->hero, $this->villain);
        $heroPos = $this->hero->getPosition();
        $villainPos = $this->villain->getPosition();

        $this->assertNotEquals($heroPos, $villainPos);
    }


    /**
     * Test if table returns card image paths correctly.
     */
    // public function testGetCardImages()
    // {
    //     $dealer = new Dealer();
    //     $deck = new DeckOfCards();
    //     $dealer->addDeck($deck);
    //     $res = $this->table->getCardImages();
    //     $exp = 50;
    //     $this->assertSame($res, $exp);

    //     $this->hero->bet(1000);
    //     $res = $this->table->getMinimumRaiseAllowed();
    //     $exp = 2000;
    //     $this->assertSame($res, $exp);
    // }
}