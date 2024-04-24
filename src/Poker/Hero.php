<?php

namespace App\Poker;

use App\Poker\TexasHandTrait;

class Hero extends Player
{
    use TexasHandTrait;

    private string $name;

    public function __construct()
    {

        parent::__construct();

        $this->name = "Mos";
        $this->currentBet = 0;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function preflopRaise(int $raiseSize, int $smallBlind): int
    {
        $raiseSize = $raiseSize - $smallBlind;
        $this->stack -= $raiseSize ;
        $this->currentBet = $raiseSize;

        return $raiseSize;
    }

    public function fold(): void
    {
        $this->hand = [];
        $this->currentBet = 0;
        $this->currentStrength = "";
    }
}
