<?php

namespace App\Poker;

use App\Poker\StrategyTrait;

class Villain extends Player
{
    use StrategyTrait;

    private $name;
    private $result;

    public function __construct() {

        parent::__construct();

        $this->name = "Teddy KGB";
        $this->result = 0;
        $this->currentBet = 0;
        
    }

    public function getName() : string {
        return $this->name;
    }

    public function raise($heroBet) : int
    {
        $raiseSize = 3 * $heroBet;
        if ($raiseSize > $this->stack) {
            $raiseSize = $this->stack;
            $this->currentBet = $raiseSize;
            $this->stack -= $raiseSize;

            return $raiseSize;
        }
        $this->stack -= ($raiseSize - $this->currentBet);
        $this->currentBet = $raiseSize;

        return $raiseSize;
    }
}