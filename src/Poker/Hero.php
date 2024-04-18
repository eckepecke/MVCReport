<?php

namespace App\Poker;

class Hero extends Player
{
    private $name;
    private $result;

    public function __construct($position = "SB") {

        parent::__construct();

        $this->name = "Mos";
        $this->result = 0;
        $this->position = $position;
        $this->currentBet = 0;
    }

    public function getName() : string {
        return $this->name;
    }

}