<?php

namespace App\Poker;

class PotManager
{

    protected int $pot;


    public function __construct()
    {
        $this->pot = 0;
    }

public function addChipsToPot(int $chips): void
    {
        $this->potSize += $chips;
    }


    public function getPotSize(): int
    {
        return $this->potSize;
    }
}