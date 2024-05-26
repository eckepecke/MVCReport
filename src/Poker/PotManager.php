<?php

namespace App\Poker;

class PotManager
{
    protected int $pot;


    public function __construct()
    {
        $this->pot = 500;
    }

    public function addChipsToPot(array $state): void
    {
        echo"ADDINGBETSTO POT";
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
        $this->pot = 500;
    }

    // public function chargeBlinds(array $players): void
    // {
    //     $blindArray = [
    //         0 => 25,
    //         1 => 50,
    //         2 => 0
    //     ];
    //     foreach ($players as $player) {
    //         $pos = $player->getPosition();
    //         $player->payBlind($blindArray[$pos]);
    //     }
    // }
}
