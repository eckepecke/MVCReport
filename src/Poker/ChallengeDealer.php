<?php

namespace App\Poker;

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


    public function __construct($playerList)
    {
        parent::__construct();

        $this->playerOne = $playerList[0];
        $this->playerTwo = $playerList[1];
        $this->handCount = 0;
    }

    // public function moveButton()
    // {
    //     $currentPosition = $this->playerOne->getPosition();
    //     echo "hej";
    //     var_dump($currentPosition);
    //     if ($currentPosition === "SB") {
    //         $this->playerOne->setPosition("BB");
    //         $this->playerTwo->setPosition("SB");
    //     } else {
    //         $this->playerOne->setPosition("SB");
    //         $currentPosition = $this->playerOne->getPosition();
    //         var_dump($currentPosition);
    //         $this->playerTwo->setPosition("BB");
    //     }

    // }

    public function incrementHandsPlayed(): void
    {
        $this->handCount += 1;
    }

    // public function dealHoleCards(): void
    // {
    //     $firstHand = $this->deck->drawOne();
    //     $secondHand = $this->deck->drawMany(2);
    //     $this->playerOne->receiveHoleCards($firstHand);
    //     $this->playerTwo->receiveHoleCards($secondHand);
    // }

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

    // public function chargeAntes(int $smallBlind, int $bigBlind) : array {
    //     $playerOnePos = $this->playerOne->getPosition();

    //     if ($playerOnePos === "SB") {
    //         $this->playerOne->payBlind($smallBlind);
    //         $this->playerTwo->payBlind($bigBlind);
    //     } else {
    //         $this->playerOne->payBlind($bigBlind);
    //         $this->playerTwo->payBlind($smallBlind);
    //     }

    //     $blinds = [$smallBlind, $bigBlind];
    //     return $blinds;
    // }

    public function randButton(): void
    {
        $seats = ["SB", "BB"];
        $position = $seats[rand(0, 1)];
        $this->playerOne->setPosition($position);
        if ($position === "SB") {
            $this->playerTwo->setPosition("BB");
        } else {
            $this->playerTwo->setPosition("SB");
        }
    }

    public function playersAllIn() :bool
    {
        $playerOneStackIsEmpty = $this->playerOne->getStack() === 0;
        $playerTwoStackIsEmpty = $this->playerTwo->getStack() === 0;

        if ($playerOneStackIsEmpty || $playerTwoStackIsEmpty) {
            return true;
        }

        return false;
    }
}
