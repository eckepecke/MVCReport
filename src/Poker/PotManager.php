<?php

namespace App\Poker;

class PotManager
{

    protected int $pot;


    public function __construct()
    {
        $this->pot = 0;
    }

public function addChipsToPot(array $state): void
    {
        $players = $state["players"];

        foreach ($players as $player) {
            $chips = $player->getCurrentBet();
            $this->pot += $chips;
        }
    }


    public function getPotSize(): int
    {
        return $this->pot;
    }

    public function emptyPot(): void
    {
        $this->pot = 0;
    }
}