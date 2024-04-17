<?php

namespace App\Poker;

use App\Poker\Hero;
use App\Poker\Villain;
use App\Poker\Challenge;



use App\Cards\DeckOfCards;


class Dealer
{
    protected $deck;
    protected $button;
    protected $playerList;
    protected $priceToPlay;

    public function __construct()
    {
        $this->deck = null;
        $this->button = null;
        $this->playerList = [];
    }

    public function addDeck(DeckOfCards $deck): void
    {
        $this->deck = $deck;
    }

    public function addPlayers(array $players): void
    {
        foreach ($players as $player) {
            $this->playerList[] = $player;
        }
    }

    public function dealFlop() :array
    {
        $flop = $this->deck->drawMany(3);
        return $flop;
    }

    public function getPriceToPlay($amountOne, $amountTwo) :int
    {
        $biggestAmount = max($amountOne, $amountTwo);
        $smallestAmount = min($amountOne, $amountTwo);

        return $biggestAmount - $smallestAmount;
    }
}