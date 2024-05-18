<?php

namespace App\FlopAndGo\Managers;

/**
 * A trait managing user input.
 */
trait HeroActionManager
{
    public function heroAction(mixed $action): void
    {
        if ($action != null && $action != "next") {
            switch ($action) {
                case "check":
                    $this->heroChecked();
                    break;
                case "call":
                    $this->heroCalled();
                    break;
                case "fold":
                    $this->heroFolded();
                    break;
                default:
                    $this->heroBet(intval($action));
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

    public function heroBet(int $amount): void
    {
        $maxBetAllowed = $this->getMaxBet($this->gameProperties['hero'], $this->gameProperties['villain']);
        $betSize = $this->heroBetSize($amount, $maxBetAllowed);
        $this->gameProperties['hero']->bet($betSize);

        $this->villainResponseToBet($betSize);
    }

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
