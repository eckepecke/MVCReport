<?php

namespace App\FlopAndGo;

use App\Gamble\Challenge;
use App\Gamble\DeckOfCards;
use App\Gamble\Table;


class Dealer
{
    protected object $deck;
    protected int $priceToPlay;


    public function addDeck(DeckOfCards $deck): void
    {
        $this->deck = $deck;
    }

    public function addTable(Table $table): void
    {
        $this->deck = $deck;
    }

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
