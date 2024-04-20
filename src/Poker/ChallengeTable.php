<?php

namespace App\Poker;

use App\Poker\Hero;
use App\Poker\Villain;
use App\Poker\Challenge;
use App\Poker\Dealer;
use App\Poker\ChallengeDealer;
use App\Poker\Table;



use App\Cards\CardGraphic;
use App\Cards\CardHand;

use App\Cards\DeckOfCards;


class ChallengeTable extends Table
{
    private $bigBlind;
    private $smallBlind;
    private $dealer;
    private $sbPlayer;
    private $bbPlayer;

    public function __construct($small, $big)
    {
        parent::__construct();
        $this->smallBlind = $small;
        $this->bigBlind = $big;
    }

    public function seatDealer(ChallengeDealer $dealer): void
    {
        $this->dealer = $dealer;
    }

    public function seatPlayers($p1, $p2): void
    {
        $this->sbPlayer = $p1;
        $this->bbPlayer = $p2;
    }

    public function getSmallBlind () : int
    {
        return $this->smallBlind;
    }

    public function getBigBlind () : int
    {
        return $this->bigBlind;
    }

    public function moveButton() : void
    {
        $temp = $this->sbPlayer;
        $this->sbPlayer = $this->bbPlayer;
        $this->bbPlayer -> $temp;
    }

    public function getSbPlayer() : object
    {
        return $this->sbPlayer;
    }

    public function getBbPlayer() : object
    {
        return $this->bbPlayer;
    }

    public function chargeAntes() : void {
        $this->sbPlayer->payBlind($this->smallBlind);
        $this->bbPlayer->payBlind($this->bigBlind);
        $this->sbPlayer->setCurrentBet($this->smallBlind);
        $this->bbPlayer->SetCurrentBet($this->bigBlind);
        $this->addChipsToPot($this->smallBlind);
        $this->addChipsToPot($this->bigBlind);
    }

    public function getPriceToPlay() :int
    {
        $amountOne = $this->bbPlayer->getCurrentBet();
        $amountTwo = $this->sbPlayer->getCurrentBet();

        $biggestAmount = max($amountOne, $amountTwo);
        $smallestAmount = min($amountOne, $amountTwo);

        return $biggestAmount - $smallestAmount;
    }
}
