<?php

namespace App\Poker;
use App\Poker\CardHand;
use App\Cards\CardGraphic;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class ShowdownManager.
 */
class ShowdownManagerTest extends TestCase
{
    private ShowdownManager $manager;
    private array $state;
    private array $board;
    private CardManager $helper;
    
    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->manager = new ShowdownManager();
        $evaluator = new SameHandEvaluator();
        $this->manager->add($evaluator);

        $this->helper = new CardManager();
        $this->helper->addEvaluator($this->manager);
        $this->state = ["players" => [new Player(), new Player(), new Player()]];
        
        $card1 = new CardGraphic();
        $card2 = new CardGraphic();
        $card1->setSuit('hearts');
        $card1->setValue(2);
        $card2->setSuit('spades');
        $card2->setValue(3);
        // This hand should win holding one pair of aces
        $losingHand1 = new CardHand();
        $losingHand1->add($card1);
        $losingHand1->add($card2);

        $this->state["players"][0]->addHand($losingHand1);

        $card3 = new CardGraphic();
        $card4 = new CardGraphic();
        $card3->setSuit('hearts');
        $card3->setValue(4);
        $card4->setSuit('spades');
        $card4->setValue('ace');

        $winningHand = new CardHand();
        $winningHand->add($card3);
        $winningHand->add($card4);
        $this->state["players"][1]->addHand($winningHand);

        $losingHand2 = new CardHand();
        $losingHand2->add($card1);
        $losingHand2->add($card2);
        $this->state["players"][2]->addHand($losingHand2);

        $card5 = new CardGraphic();
        $card6 = new CardGraphic();
        $card7 = new CardGraphic();
        $card8 = new CardGraphic();
        $card9 = new CardGraphic();

        $card5->setSuit('clubs');
        $card5->setValue(9);
        $card6->setSuit('clubs');
        $card6->setValue(10);
        $card7->setSuit('clubs');
        $card7->setValue(2);
        $card8->setSuit('diamonds');
        $card8->setValue('king');
        $card9->setSuit('diamonds');
        $card9->setValue('ace');

        $this->board = [$card5,$card6,$card7,$card8,$card9];
    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateShowdownManager()
    {
        $this->assertInstanceOf("\App\Poker\ShowdownManager", $this->manager);
    }

    /**
     * Test the to find a winning player.
     */
    public function testFindOneWinner()
    {
        $expWinner = $this->state["players"][1];
        $this->helper->updateHandStrengths($this->state["players"], $this->board);

        $winner = $this->manager->findWinner($this->state["players"], $this->board);
        $this->assertSame($expWinner, $winner);

        // After making hand1 the have a stronger ace
        // the first player should now win.
        $card1 = new CardGraphic();
        $card2 = new CardGraphic();
        $card1->setSuit('hearts');
        $card1->setValue('ace');
        $card2->setSuit('spades');
        $card2->setValue('queen');
        $newHand = new CardHand();
        $newHand->add($card1);
        $newHand->add($card2);

        $this->state["players"][0]->resetHand();
        $this->state["players"][0]->addHand($newHand);

        $newWinner = $this->state["players"][0];

        $this->helper->updateHandStrengths($this->state["players"], $this->board);
        $winner = $this->manager->findWinner($this->state["players"], $this->board);

        $winner = $this->manager->findWinner($this->state["players"], $this->board);
        $this->assertSame($newWinner, $winner);

        $this->manager->getWinner();
    }

    /**
     * Test the flsuh comparison.
     */
    public function testFlushComparison()
    {

        // This hand contains the nut flush
        $card1 = new CardGraphic();
        $card2 = new CardGraphic();
        $card1->setSuit('clubs');
        $card1->setValue('ace');
        $card2->setSuit('clubs');
        $card2->setValue('queen');
        $winningFlush = new CardHand();
        $winningFlush->add($card1);
        $winningFlush->add($card2);

        $this->state["players"][0]->resetHand();
        $this->state["players"][0]->addHand($winningFlush);

        $expWinner = $this->state["players"][0];

        // This hand contains a losing flush
        $card1 = new CardGraphic();
        $card2 = new CardGraphic();
        $card1->setSuit('clubs');
        $card1->setValue('ace');
        $card2->setSuit('clubs');
        $card2->setValue('queen');
        $losingFlush = new CardHand();
        $losingFlush->add($card1);
        $losingFlush->add($card2);

        $this->state["players"][1]->resetHand();
        $this->state["players"][1]->addHand($losingFlush);

        $this->helper->updateHandStrengths($this->state["players"], $this->board);
        $winner = $this->manager->findWinner($this->state["players"], $this->board);

        $this->assertSame($expWinner, $winner);

        $this->manager->getWinner();
    }

    // /**
    //  * Test the fullhose comparison.
    //  */
    // public function testFullHouseAndQuadsComparison()
    // {

    //     // This hand contains the nut fullhouse.
    //     $card1 = new CardGraphic();
    //     $card2 = new CardGraphic();
    //     $card1->setSuit('clubs');
    //     $card1->setValue('ace');
    //     $card2->setSuit('diamonds');
    //     $card2->setValue('king');

    //     $boardCard = new CardGraphic();
    //     $boardCard->setSuit('hearts');
    //     $boardCard->setValue('king');
    //     $this->board[0] = $boardCard;
    //     // board now reads:
    //     // K, 10, 2, K, A

    //     $winningFull = new CardHand();
    //     $winningFull->add($card1);
    //     $winningFull->add($card2);

    //     $this->state["players"][0]->resetHand();
    //     $this->state["players"][0]->addHand($winningFull);

    //     $expWinner = $this->state["players"][0];

    //     // This hand contains a worse full house.
    //     $card4 = new CardGraphic();
    //     $card5 = new CardGraphic();
    //     $card4->setSuit('spades');
    //     $card4->setValue(10);
    //     $card5->setSuit('diamonds');
    //     $card5->setValue(10);
    //     $losingFull = new CardHand();
    //     $losingFull->add($card4);
    //     $losingFull->add($card5);

    //     $this->state["players"][1]->resetHand();
    //     $this->state["players"][1]->addHand($losingFull);

    //     $this->helper->updateHandStrengths($this->state["players"], $this->board);
    //     $winner = $this->manager->findWinner($this->state["players"], $this->board);

    //     $winner = $this->manager->findWinner($this->state["players"], $this->board);
    //     $this->assertSame($expWinner, $winner);

    //     $this->manager->getWinner();

    //     $boardCard10 = new CardGraphic();
    //     $boardCard10->setSuit('test');
    //     $boardCard->setValue(10);
    //     $this->board[1] = $boardCard10;
    //     $this->board[2] = $boardCard10;

    //     $boardCardKing = new CardGraphic();
    //     $boardCardKing->setSuit('test');
    //     $boardCardKing->setValue('king');
    //     $this->board[4] = $boardCardKing;


    //     // board now reads:
    //     // K, 10, 10, K, A

    //     $expWinner = $this->state["players"][0];
    //     $this->helper->updateHandStrengths($this->state["players"], $this->board);
    //     $winner = $this->manager->findWinner($this->state["players"], $this->board);

    //     $winner = $this->manager->findWinner($this->state["players"], $this->board);
    //     $this->assertSame($expWinner, $winner);
    // }

    /**
     * Test the fullhose comparison.
     */
    public function testStraightsComparison()
    {

        // This hand contains the nut fullhouse.

        $card1 = new CardGraphic();
        $card2 = new CardGraphic();
        $card3 = new CardGraphic();
        $card4 = new CardGraphic();
        $card5 = new CardGraphic();
        $card1->setSuit('clubs');
        $card1->setValue(6);
        $card2->setSuit('diamonds');
        $card2->setValue(5);
        $card3->setSuit('diamonds');
        $card3->setValue(4);
        $card4->setSuit('diamonds');
        $card4->setValue('king');
        $card5->setSuit('diamonds');
        $card5->setValue('ace');
        // Update board to have possible straights
        // Board reads:
        // 6, 5, 4, K, A
        $this->board = [$card1,$card2,$card3,$card4,$card5];

        $card6 = new CardGraphic();
        $card7 = new CardGraphic();
        $card8 = new CardGraphic();
        $card9 = new CardGraphic();
        $card10 = new CardGraphic();
        $card11 = new CardGraphic();
        //Hand1 has the nut straight
        $newHand1 = new CardHand();
        $card6->setSuit('test');
        $card6->setValue(7);
        $card7->setSuit('test');
        $card7->setValue(8);
        $newHand1->add($card6);
        $newHand1->add($card7);
        $this->state["players"][0]->resetHand();
        $this->state["players"][0]->addHand($newHand1);
        //Hand2 has the second strongest straight
        $newHand2 = new CardHand();
        $card8->setSuit('test');
        $card8->setValue(7);
        $card9->setSuit('test');
        $card9->setValue(3);
        $newHand2->add($card8);
        $newHand2->add($card9);
        $this->state["players"][1]->resetHand();
        $this->state["players"][1]->addHand($newHand2);
        //Hand3 has the third strongest straight
        $newHand3 = new CardHand();
        $card10->setSuit('test');
        $card10->setValue(2);
        $card11->setSuit('test');
        $card11->setValue(3);
        $newHand3->add($card10);
        $newHand3->add($card11);
        $this->state["players"][2]->resetHand();
        $this->state["players"][2]->addHand($newHand3);

        $expWinner = $this->state["players"][0];
        $this->helper->updateHandStrengths($this->state["players"], $this->board);
        $winner = $this->manager->findWinner($this->state["players"], $this->board);

        $this->assertSame($expWinner, $winner);
    }

    /**
     * Test the hicards comparison.
     */
    public function testHighCardsComparison()
    {
        $this->board = [];
        $expWinner = $this->state["players"][1];
        $this->helper->updateHandStrengths($this->state["players"], $this->board);
        $winner = $this->manager->findWinner($this->state["players"], $this->board);

        $this->assertSame($expWinner, $winner);
    }

    /**
     * Test the hicards comparison.
     */
    public function testHighTwoPairComparison()
    {
        $card1 = new CardGraphic();
        $card2 = new CardGraphic();
        //Hand1 has winning 2pair
        $winningHand = new CardHand();
        $card1->setSuit('test');
        $card1->setValue(10);
        $card2->setSuit('test');
        $card2->setValue('ace');
        $winningHand->add($card1);
        $winningHand->add($card2);


        $card3 = new CardGraphic();
        $card4 = new CardGraphic();
        //Hand2 has losing 2pair
        $losingHand= new CardHand();
        $card3->setSuit('test');
        $card3->setValue(2);
        $card4->setSuit('test');
        $card4->setValue('ace');
        $losingHand->add($card3);
        $losingHand->add($card4);

        $this->state["players"][0]->resetHand();
        $this->state["players"][1]->resetHand();
        $this->state["players"][2]->resetHand();


        $this->state["players"][0]->addHand($winningHand);
        $this->state["players"][1]->addHand($losingHand);
        $this->state["players"][2]->addHand($losingHand);


        $expWinner = $this->state["players"][0];
        $this->helper->updateHandStrengths($this->state["players"], $this->board);
        $winner = $this->manager->findWinner($this->state["players"], $this->board);

        $this->assertSame($expWinner, $winner);


        // $winningHand = new CardHand();
        // $card1->setSuit('test');
        // $card1->setValue('ace');
        // $card2->setSuit('test');
        // $card2->setValue('ace');
        // $winningHand->add($card1);
        // $winningHand->add($card2);

        // $this->state["players"][2]->resetHand();
        // $this->state["players"][2]->addHand($winningHand);

        // $expWinner = $this->state["players"][2];
        // $this->helper->updateHandStrengths($this->state["players"], $this->board);
        // $winner = $this->manager->findWinner($this->state["players"], $this->board);

        // $winner = $this->manager->findWinner($this->state["players"], $this->board);
        // $this->assertSame($expWinner, $winner);

    }
}