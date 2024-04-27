<?php

namespace App\Poker;

use App\Poker\Hero;
use App\Poker\Villain;
use App\Poker\Challenge;
use App\Cards\DeckOfCards;

class Dealer
{
    protected object $deck;
    protected int $priceToPlay;


    public function addDeck(DeckOfCards $deck): void
    {
        $this->deck = $deck;
    }

    public function addTable(SpecialTable $table): void
    {
        $this->deck = $deck;
    }

    // public function addPlayers(array $players): void
    // {
    //     foreach ($players as $player) {
    //         $this->playerList[] = $player;
    //     }
    // }

    public function dealFlop(): array
    {
        $flop = $this->deck->drawMany(3);
        return $flop;
    }

    public function dealOne(): object
    {
        $card = $this->deck->drawOne();
        return $card;
    }

    public function dealRemaining(array $board): array
    {
        $remaining = 5 - count($board);
        echo "remaining: $remaining";
        $cards = $this->deck->drawMany($remaining);
        return $cards;
    }
}
