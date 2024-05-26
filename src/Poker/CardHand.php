<?php

namespace App\Poker;

use App\Cards\CardGraphic;
use App\Poker\TexasHandTrait;

class CardHand
{
    use TexasHandTrait;

    private array $hand = [];
    private string $strengthString = "";
    private int $strengthInt = 0;



    public function add(CardGraphic $card): void
    {
        $this->hand[] = $card;
    }

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

    public function getValueArray(): array
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

    public function getCardArray(): array
    {
        return $this->hand;
    }

    // public function fuseWithCommunityCards(array $board): void
    // {
    //     $this->hand = array_merge($this->hand, $board);
    // }

    public function getStrengthString(): string
    {
        return $this->strengthString;
    }

    public function setStrengthString(string $strength): void
    {
        $this->strengthString = $strength;
    }

    public function getStrengthInt(): int
    {
        return $this->strengthInt;
    }

    public function setStrengthInt(int $strength): void
    {
        $this->strengthInt = $strength;
    }
}
