<?php

namespace App\Poker;

use App\Poker\StrategyTrait;

class Villain extends Player
{
    use StrategyTrait;

    private $name;
    private $result;

    public function __construct($position = "BB") {

        parent::__construct();

        $this->name = "Teddy KGB";
        $this->result = 0;
        $this->position = $position;
        $this->currentBet = 0;
        
    }

    public function getName() : string {
        return $this->name;
    }
}