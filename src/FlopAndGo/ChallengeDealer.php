<?php

namespace App\FlopAndGo;

use App\Poker\Hero;
use App\Poker\Villain;
use App\Poker\Challenge;
use App\Poker\Dealer;


use App\Cards\CardGraphic;
use App\Cards\CardHand;

use App\Cards\DeckOfCards;

class ChallengeDealer extends Dealer
{
    private object $playerOne;
    private object $playerTwo;
    private int $handCount;
    private object $table;


    public function __construct(array $playerList)
    {
        parent::__construct();

        $this->playerOne = $playerList[0];
        $this->playerTwo = $playerList[1];
        $this->handCount = 0;
    }

    public function addTable(ChallengeTable $table): void
    {
        $this->table = $table;
    }

    public function incrementHandsPlayed(): void
    {
        $this->handCount += 1;
    }

    public function dealHoleCards(): void
    {
        $firstCard = $this->deck->drawOne();
        $this->playerOne->receiveCard($firstCard);

        $secondCard = $this->deck->drawOne();
        $this->playerTwo->receiveCard($secondCard);

        $thirdCard = $this->deck->drawOne();
        $this->playerOne->receiveCard($thirdCard);

        $fourthCard = $this->deck->drawOne();
        $this->playerTwo->receiveCard($fourthCard);
    }

    public function playersAllIn(): bool
    {
        $playerOneBroke = $this->playerOne->getStack() === 0;
        $playerTwoBroke = $this->playerTwo->getStack() === 0;

        if ($playerOneBroke || $playerTwoBroke) {
            return true;
        }

        return false;
    }

    public function moveChipsAfterFold(): void
    {
        $firstBet = $this->playerOne->getCurrentBet();
        $secondBet = $this->playerTwo->getCurrentBet();
        $this->table->addChipsToPot($firstBet);
        $this->table->addChipsToPot($secondBet);
        $pot = $this->table->getPotSize();

        $winner = $this->playerOne;
        $biggestBet = max($firstBet, $secondBet);
        if ($biggestBet === $secondBet) {
            $winner = $this->playerTwo;
        }
        $winner->takePot($pot);
    }

    public function resetForNextHand(): void
    {
        $this->playerOne->fold();
        $this->playerTwo->fold();
        $this->table->cleanTable();
    }

    public function dealToShowdown(): void
    {
        $board = $this->table->getBoard();
        $cards = $this->dealRemaining($board);
        $this->table->registerMany($cards);
    }
}
