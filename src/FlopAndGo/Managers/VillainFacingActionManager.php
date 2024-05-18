<?php

namespace App\FlopAndGo\Managers;

/**
 * A trait managing villain opportunities.
 */
trait VillainFacingActionManager
{

    public function villainResponseToBet(int $amount): void
    {
        $action = $this->villain->actionFacingBet();

        if ($action != null && $action != "next") {
            switch ($action) {
                case "fold":
                    $this->villainFoldVBet();
                    break;
                case "call":
                    $this->villainCallBet($amount);
                    break;
                default:
                    $this->villainRaisedVBet($amount);
                    break;
            }
        }
    }

    public function villainFoldVBet() {
        $this->table->addChipsToPot($this->villain->getCurrentBet());
        $this->table->addChipsToPot($this->hero->getCurrentBet());
        $this->villain->fold();
        $this->hero->takePot($this->table->getPotSize());
        $this->hero->fold();
        $this->table->cleanTable();
        $this->challenge->incrementHandsPlayed();
        $this->newHand = true;
    }

    public function villainCallBet($amount) {
        $this->villain->call($amount);
        $this->table->addChipsToPot($this->villain->getCurrentBet());
        $this->table->addChipsToPot($this->hero->getCurrentBet());
        $this->villain->resetCurrentBet();
        $this->hero->resetCurrentBet();
        $this->allInCheck($this->villain);
        echo "villain called inc";

        $this->incrementStreet();
    }

    public function villainRaisedVBet($amount) {
        if (($this->hero->isAllin())) {
            $this->villain->call($amount);
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->dealer->dealToShowdown();
            $this->showdown();
            return;

        }

        if ($amount >= ($this->villain->getStack() + $this->villain->getCurrentBet()) || $this->hero->isAllin()) {
            $this->villain->call($amount);

            $this->allInCheck($this->villain);
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->allInCheck($this->villain);
            $this->newHand = true;
            echo "cillain called shove inc";

            $this->incrementStreet();
            return;
        }

        $this->villain->raise($amount);
    }

}
