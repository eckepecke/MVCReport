<?php

namespace App\Cards;

use App\Cards\CardGraphic;

class CardHand
{
    private array $hand = [];

    public function add(CardGraphic $card): void
    {
        $this->hand[] = $card;
    }

    // public function pull(): void
    // {
    //     foreach ($this->hand as $card) {
    //         $card->getCard();
    //     }
    // }

    public function getNumberCards(): int
    {
        return count($this->hand);
    }

    public function getCardValues(): array
    {
        $values = [];
        foreach ($this->hand as $card) {
            $values[] = $card->getCardString();
        }
        return $values;
    }

    public function getImgNames(): array
    {
        $values = [];
        foreach ($this->hand as $card) {
            $values[] = $card->getImgName();
        }
        return $values;
    }

    public function getCards(): array
    {
        return $this->hand;

    }
}
