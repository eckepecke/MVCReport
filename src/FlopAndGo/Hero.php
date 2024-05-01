<?php

namespace App\FlopAndGo;

use App\FlopAndGo\TexasHandTrait;

class Hero extends Player
{
    use TexasHandTrait;

    private string $name;
    private int $startStack;

    public function __construct()
    {

        parent::__construct();

        $this->name = "Mike";
        $this->currentBet = 0;
        $this->startStack = 5000;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function fold(): void
    {
        $this->hand = [];
        $this->currentBet = 0;
        $this->currentStrength = "";
        $this->lastAction = "fold";
    }

    public function getStartStack(): int
    {
        return $this->startStack;

    }
}
