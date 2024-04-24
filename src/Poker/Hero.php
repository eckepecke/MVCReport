<?php

namespace App\Poker;

use App\Poker\TexasHandTrait;


class Hero extends Player
{
    use TexasHandTrait;

    private $name;
    private $result;

    public function __construct()
    {

        parent::__construct();

        $this->name = "Mos";
        $this->result = 0;
        $this->currentBet = 0;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function preflopRaise($raiseSize, $smallBlind): int
    {
        $raiseSize = $raiseSize - $smallBlind;
        $this->stack -= $raiseSize ;
        $this->currentBet = $raiseSize;

        return $raiseSize;
    }

}
