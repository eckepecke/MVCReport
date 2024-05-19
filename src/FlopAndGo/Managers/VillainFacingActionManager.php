<?php

namespace App\FlopAndGo\Managers;

/**
 * A trait managing villain opportunities.
 */
trait VillainFacingActionManager
{
    public function villainResponseToBet(int $amount): void
    {
        $action = $this->gameProperties['villain']->actionFacingBet();

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

    public function villainFoldVBet()
    {
        $this->gameProperties['table']->addChipsToPot($this->gameProperties['villain']->getCurrentBet());
        $this->gameProperties['table']->addChipsToPot($this->gameProperties['hero']->getCurrentBet());
        $this->gameProperties['villain']->fold();
        $this->gameProperties['hero']->takePot($this->gameProperties['table']->getPotSize());
        $this->gameProperties['hero']->fold();
        $this->gameProperties['table']->cleanTable();
        $this->gameProperties['challenge']->incrementHandsPlayed();
        $this->newHand = true;
    }

    public function villainCallBet($amount)
    {
        $this->gameProperties['villain']->call($amount);
        $this->gameProperties['table']->addChipsToPot($this->gameProperties['villain']->getCurrentBet());
        $this->gameProperties['table']->addChipsToPot($this->gameProperties['hero']->getCurrentBet());
        $this->gameProperties['villain']->resetCurrentBet();
        $this->gameProperties['hero']->resetCurrentBet();
        $this->allInCheck($this->gameProperties['villain']);

        $this->incrementStreet();
    }

    public function villainRaisedVBet($amount)
    {
        if (($this->gameProperties['hero']->isAllin())) {
            $this->gameProperties['villain']->call($amount);
            $this->gameProperties['table']->addChipsToPot($this->gameProperties['villain']->getCurrentBet());
            $this->gameProperties['table']->addChipsToPot($this->gameProperties['villain']->getCurrentBet());
            $this->gameProperties['dealer']->dealToShowdown();
            $this->showdown();
            return;

        }

        if ($amount >= ($this->gameProperties['villain']->getStack() + $this->gameProperties['villain']->getCurrentBet()) || $this->gameProperties['hero']->isAllin()) {
            $this->gameProperties['villain']->call($amount);

            $this->allInCheck($this->gameProperties['villain']);
            $this->gameProperties['table']->addChipsToPot($this->gameProperties['hero']->getCurrentBet());
            $this->gameProperties['table']->addChipsToPot($this->gameProperties['villain']->getCurrentBet());
            $this->allInCheck($this->gameProperties['villain']);
            $this->newHand = true;

            $this->incrementStreet();
            return;
        }

        $this->gameProperties['villain']->raise($amount);
    }

}
