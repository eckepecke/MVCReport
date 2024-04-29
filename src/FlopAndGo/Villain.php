<?php

namespace App\FlopAndGo;


use App\FlopAndGo\StrategyTrait;
use App\FlopAndGo\TexasHandTrait;

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
        echo "villain raise()";
        $raiseSize = 3 * $heroBet;
        if ($raiseSize > $this->stack) {
            $raiseSize = $this->stack;
            $this->currentBet = $this->stack + $this->currentBet;
            $this->stack -= $raiseSize;

            return $raiseSize;
        }
        $this->stack -= ($raiseSize - $this->currentBet);
        $this->currentBet = $raiseSize;
        echo "villain current bet";
        var_dump($this->currentBet);

        $this->lastAction = "raise";

        return $raiseSize;
    }

    public function fold(): void
    {
        $this->hand = [];
        $this->currentBet = 0;
        $this->currentStrength = "";
        $this->lastAction = "fold";
    }
}
