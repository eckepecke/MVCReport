<?php

namespace App\Poker;

class CommunityCardManager
{
    protected array $board;

    public function __construct()
    {
        $this->board = [];
    }

    public function register(array $cards): void
    {
        foreach ($cards as $card) {
            $this->board[] = $card;
        }
    }

    public function getBoard(): array
    {
        return $this->board;
    }

    public function cleanTable(): void
    {
        $this->board = [];
    }

    public function cardsDealt(): int
    {
        $cardsDealt = count($this->board);
        return $cardsDealt;
    }
}
