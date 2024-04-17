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

    public function getSmallBlind () : int
    {
        return $this->smallBlind;
    }

    public function getBigBlind () : int
    {
        return $this->smallBlind;
    }
}
