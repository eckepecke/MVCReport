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

    public function dealFlop() : array
    {
        $flop = $this->deck->drawMany(3);
        return $flop;
    }

    public function dealOne() : object
    {
        $card = $this->deck->drawOne();
        return $card;
    }

    public function dealRemaining(array $board) : array
    {
        $remaining = 5 - count($board);
        echo "remaining: $remaining";
        $cards = $this->deck->drawMany($remaining);
        return $cards;
    }
}