<?php

namespace App\FlopAndGo;

/**
 * A trait managing user input.
 */
trait HeroActionManager
{
    public function heroFolded() : void 
    {
        $this->hero->fold();
        $this->villain->takePot($this->table->getPotSize());
        $this->villain->fold();
        $this->table->cleanTable();
        $this->newHand = true;
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
            $this->newHand = true;

            // might add strret variable here
            return;
        }

        if ($action === "call") {
            $this->villain->call($amount);

            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->villain->resetCurrentBet();
            $this->hero->resetCurrentBet();
            //$this->table->nextStreet();
            return;
        }

        if ($amount >= ($this->villain->getStack() + $this->villain->getCurrentBet())){
            $this->villain->call($amount);
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->newHand = true;
            return;
        }
        $this->villain->raise($amount);
    }

    public function heroChecked() : void 
    {
        $this->hero->check();
    }
}