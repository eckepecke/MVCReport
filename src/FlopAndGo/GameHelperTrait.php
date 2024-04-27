<?php

namespace App\FlopAndGo;

/**
 * A trait implementing histogram for integers.
 */
trait GameHelperTrait
{
    public function handSetUp() : void
    {
        $this->table->getBombPotChips();
        $this->dealer->dealHoleCards();
    }

    public function heroFolded() : void 
    {
        $this->hero->fold();
        $this->villain->takePot($this->table->getPotSize());
        $this->villain->fold();
        $this->table->cleanTable();
    }

    public function heroCalled() : void 
    {
        ///Denna route har int prövats
        $villainBet = $this->villain->getCurrentBet();
        $this->hero->call($villainBet);
        $this->table->addChipsToPot($villainBet);
        $this->table->addChipsToPot($this->hero->getCurrentBet());
        $this->villain->resetCurrentBet();
        $this->hero->resetCurrentBet();
    }

    public function heroBet($amount) {
        $this->hero->bet($amount);
        $action = $this->villain->actionFacingBet();
        //var_dump($action);
        $action = "fold";
        if ($action === "fold") {
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->villain->fold();
            $this->hero->takePot($this->table->getPotSize());
            $this->hero->fold();
            $this->table->cleanTable();
            return;
        }

        if ($action === "call") {
            $this->villain->call($amount);

            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->villain->resetCurrentBet();
            $this->hero->resetCurrentBet();
            return;
        }

        if ($amount >= ($this->villain->getStack() + $this->villain->getCurrentBet())){
            $this->villain->call($amount);
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            return;
        }
        $this->villain->raise($amount);
    }
}