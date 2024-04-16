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
    private $playerOne;
    private $playerTwo;
    private $handCount;


    public function __construct($playerList)
    {
        parent::__construct();

        $this->playerOne = $playerList[0];
        $this->playerTwo = $playerList[1];
        $this->handCount = 0;
    }

    public function moveButton()
    {
        $currentPosition = $this->playerOne->getPosition();
        if ($currentPosition === "BTN") {
            $this->playerOne->setPosition("BB");
            $this->playerTwo->setPosition("BTN");
        }
        $this->playerOne->setPosition("BTN");
        $this->playerTwo->setPosition("BB");
    }

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

    public function chargeAntes(int $smallBlind, int $bigBlind) : array {
        $playerOnePos = $this->playerOne->getPosition();

        if ($playerOnePos === "SB") {
            $this->playerOne->payBlind($smallBlind);
            $this->playerTwo->payBlind($bigBlind);
        } else {
            $this->playerOne->payBlind($bigBlind);
            $this->playerTwo->payBlind($smallBlind);
        }

        $blinds = [$smallBlind, $bigBlind];
        return $blinds;
    }
}