<?php

namespace App\Poker;

/**
 * A class managing user input.
 */
class HeroActionManager
{
    /**
     * Handles user input.
     */
    public function heroMove(mixed $action, object $hero): void
    {
        if ($action != null && $action != "next") {
            switch ($action) {
                case "check":
                    $hero->check();
                    break;
                case "call":
                    $hero->call();
                    break;
                case "fold":
                    $hero->fold();
                    break;
                default:
                    $hero->bet(intval($action));
                    break;
            }
        }
    }

    public function heroFolded(): void
    {
        $this->gameProperties['table']->addChipsToPot($this->gameProperties['villain']->getCurrentBet());
        $this->gameProperties['table']->addChipsToPot($this->gameProperties['hero']->getCurrentBet());
        $this->gameProperties['hero']->fold();
        $this->gameProperties['villain']->takePot($this->gameProperties['table']->getPotSize());
        $this->gameProperties['villain']->fold();
        $this->gameProperties['table']->cleanTable();
        $this->gameProperties['challenge']->incrementHandsPlayed();
        $this->setNewHandTrue();
    }

    public function heroCalled(): void
    {
        ///Denna route har int prövats
        $villainBet = $this->gameProperties['villain']->getCurrentBet();
        $this->gameProperties['hero']->call($villainBet);
        $this->gameProperties['table']->addChipsToPot($villainBet);
        $this->gameProperties['table']->addChipsToPot($this->gameProperties['hero']->getCurrentBet());
        $this->gameProperties['villain']->resetCurrentBet();
        $this->gameProperties['hero']->resetCurrentBet();
        $this->allInCheck($this->gameProperties['villain']);
        $this->allInCheck($this->gameProperties['hero']);
        $this->incrementStreet();
    }

    // public function heroBet(int $amount, object $player): void
    // {
    //     $player->bet($amount);
    // }

    public function heroChecked(): void
    {
        $this->gameProperties['hero']->check();
        if ($this->gameProperties['hero']->getPosition() === "SB") {
            $this->incrementStreet();
        }
    }

    public function heroBetSize(int $amount, int $maxBet): int
    {
        return min($amount, $maxBet);
    }
}
