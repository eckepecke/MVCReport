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
        var_dump($this->villain->getCurrentBet());

        var_dump($this->hero->getCurrentBet());
        $this->table->addChipsToPot($villainBet);
        $this->table->addChipsToPot($this->hero->getCurrentBet());
        $this->villain->resetCurrentBet();
        $this->hero->resetCurrentBet();
        var_dump($this->villain->getCurrentBet());

        var_dump($this->hero->getCurrentBet());

    }

    public function heroBet($amount) {
        echo "triggered";
        $this->hero->bet($amount);
        $action = $this->villain->actionFacingBet();
        var_dump($action);
        $action = "raise";
        if ($action === "fold") {
            ///debug here
            echo"vaillain fold";
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->villain->fold();
            $this->hero->takePot($this->table->getPotSize());
            $this->hero->fold();
            $this->table->cleanTable();
            return;
        }

        if ($action === "call") {
            // var_dump($this->villain->getCurrentBet());
            // var_dump($this->hero->getCurrentBet());
            // var_dump($crash);
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->villain->call($amount);
            $this->villain->resetCurrentBet();
            $this->hero->resetCurrentBet();
        }
        // we reach this if $action = raise
        //make sure villain has enough chips to raise
        if ($amount >= ($this->villain->getStack() + $this->villain->getCurrentBet())){
            $this->villain->call($amount);
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            return;
        }
        $this->villain->raise($amount);
    }
}