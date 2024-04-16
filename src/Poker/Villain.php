<?php

namespace App\Poker;

class Villain extends Player
{
    private $name;
    private $result;

    public function __construct($position = "BB") {

        parent::__construct();

        $this->name = "Teddy KGB";
        $this->result = 0;
        $this->position = $position;
    }

    public function getName() : string {
        return $this->name;
    }
}