<?php

namespace App\Poker;

use App\Poker\StrategyTrait;
use App\Poker\TexasHandTrait;

class Villain extends Player
{
    use StrategyTrait;
    use TexasHandTrait;

    private string $name;

    public function __construct()
    {

        parent::__construct();

        $this->name = "Teddy KGB";
        $this->currentBet = 0;

    }

    public function getName(): string
    {
        return $this->name;
    }

    public function raise(int $heroBet): int
    {
        $raiseSize = 3 * $heroBet;
        if ($raiseSize > $this->stack) {
            $raiseSize = $this->stack;
            $this->currentBet = $this->stack + $this->currentBet;
            $this->stack -= $raiseSize;

            return $raiseSize;
        }
        $this->stack -= ($raiseSize - $this->currentBet);
        $this->currentBet = $raiseSize;

        return $raiseSize;
    }
}
