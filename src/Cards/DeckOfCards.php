<?php

namespace App\Cards;

use App\Cards\CardGraphic;

class DeckOfCards
{
    private array $cards = [];

    public function __construct()
    {
        $this->initializeCards();
    }

    public function initializeCards(): void
    {
        $this->cards = [];

        $suits = ['diamonds', 'hearts', 'clubs', 'spades'];

        // Iterate over each suit
        foreach ($suits as $suit) {
            // Iterate over each value
            foreach (['ace', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'jack', 'queen', 'king'] as $value) {
                // Create a new CardGraphic object
                $cardGraphic = new CardGraphic();

                // Set the suit and value of the card graphic
                $cardGraphic->setSuit($suit);
                $cardGraphic->setValue($value);

                // Add the card graphic to the cards array
                $this->cards[] = $cardGraphic;
            }
        }
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    public function getImgNames(): array
    {
        $imgNames = [];
        foreach ($this->cards as $card) {
            $imgNames[] = $card->getImgName();
        }
        return $imgNames;
    }

    public function shuffleDeck(): void
    {
        shuffle($this->cards);
    }

    public function size(): int
    {
        return count($this->cards);
    }

    // public function drawOne(int $index = null): object
    // {
    //     if ($index !== null) {
    //         $card = $this->cards[$index];
    //         unset($this->cards[$index]);
    //     } else {
    //         $length = count($this->cards);
    //         //$randIndex = rand(0, $length - 1);
    //         $randIndex = 0;

    //         $card = $this->cards[$randIndex];
    //         unset($this->cards[$randIndex]);
    //     }

    //     $this->cards = array_values($this->cards);

    //     return $card;
    // }

    public function drawOne(int $index = null): object
    {
        $index = ($index !== null) ? $index : 0;
        $card = $this->cards[$index];
        unset($this->cards[$index]);

        $this->cards = array_values($this->cards);

        return $card;
    }

    public function drawMany(int $amount): array
    {
        $randIndexArray = [];
        $deckSize = $this->size();

        // for ($i = 0; $i < $amount; $i++) {
        //     $randIndex = rand(0, $deckSize - 1);
        //     if (in_array($randIndex, $randIndexArray)) {
        //         $i--;
        //     } else {
        //         $randIndexArray[] = $randIndex;
        //     }
        // }

        $count = 0;
        while ($count < $amount) {
            $randIndex = rand(0, $deckSize - 1);
            if (!in_array($randIndex, $randIndexArray)) {
                $randIndexArray[] = $randIndex;
                $count++;
            }
        }

        $drawnCards = [];

        foreach ($randIndexArray as $index) {
            $drawnCards[] = $this->cards[$index];
            unset($this->cards[$index]);
        }

        $this->cards = array_values($this->cards);

        return $drawnCards;
    }
}
