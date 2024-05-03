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
        $this->table->addChipsToPot($this->villain->getCurrentBet());
        $this->table->addChipsToPot($this->hero->getCurrentBet());
        $this->hero->fold();
        $this->villain->takePot($this->table->getPotSize());
        $this->villain->fold();
        $this->table->cleanTable();
        $this->challenge->incrementHandsPlayed();
        $this->newHand = true;
    }

    public function heroCalled(): void
    {
        ///Denna route har int prövats
        $villainBet = $this->villain->getCurrentBet();
        $this->hero->call($villainBet);
        $this->table->addChipsToPot($villainBet);
        $this->table->addChipsToPot($this->hero->getCurrentBet());
        $this->villain->resetCurrentBet();
        $this->hero->resetCurrentBet();
        $this->allInCheck($this->villain);
        $this->allInCheck($this->hero);

        $this->incrementStreet();
    }

    public function heroBet(int $amount): void
    {
        $maxBetAllowed = $this->getMaxBet($this->hero, $this->villain);
        $betSize = $this->heroBetSize($amount, $maxBetAllowed);
        $this->hero->bet($betSize);

        $this->villainResponseToBet($betSize);
    }

    public function heroChecked(): void
    {
        $this->hero->check();
        if ($this->hero->getPosition() === "SB") {
            $this->incrementStreet();
        }
    }

    public function heroBetSize(int $amount, int $maxBet): int
    {
        return min($amount, $maxBet);
    }
}
