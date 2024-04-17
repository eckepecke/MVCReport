<?php

namespace App\Poker;

class ActionSequence
{
    private $sequence;

    public function __construct()
    {
        $this->sequence = [];
    }

    public function addAction(string $action) : void
    {
        $this->sequence[] = $action;
    }

    public function resetSequence()
    {
        $this->sequence = [];
    }

    public function getSequence()
    {
        return $this->sequence;
    }
}
