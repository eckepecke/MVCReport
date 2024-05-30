<?php

namespace App\Poker;

class PotManager
{
    protected int $pot;


    public function __construct($chips)
    {
        $this->pot = $chips;
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

    public function resetPot(): void
    {
        $this->pot = 0;
    }

    public function chargeBlinds(array $players): void
    {
        $blindArray = [
            0 => 100,
            1 => 200,
            2 => 400
        ];
        foreach ($players as $player) {
            $pos = $player->getPosition();
            $player->payBlind($blindArray[$pos]);
        }
    }
}
