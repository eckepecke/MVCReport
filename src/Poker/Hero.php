<?php

namespace App\Poker;

use App\Poker\CardHand;

class Hero extends Player
{

    protected bool $isHero = true;


    public function __construct()
    {
        parent::__construct();
        $this->isHero = false;
        $this->stack = 500;
    }

    public function isHero(): bool
    {
        return $this->isHero;
    }
}
