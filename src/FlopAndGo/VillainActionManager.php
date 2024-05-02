<?php

namespace App\FlopAndGo;

/**
 * A trait managing villain opportunities.
 */
trait VillainActionManager
{
    public function villainAction() : void 
    {
        $villainPos = $this->villain->getPosition();
        $action = $this->villain->betOpportunity();

        if ($villainPos === "BB") {
            if ($action === "check") {
                echo"villain checking OOP";
                $this->villain->check();
                return;
            }
        }

        if ($villainPos === "SB") {
            if ($action === "check") {
                $this->villain->check();
                echo"villain checking IP";
                $this->incrementStreet();
                return;
            }
        }

        $betSize = $this->villain->randBetSize($this->table->getPotSize());
        if ($betSize > $this->hero->getStack()) {
            $betSize = $this->hero->getStack();
        }
        $this->villain->bet($betSize);

        //$this->table->addChipsToPot($this->villain->getCurrentBet());
    }

    public function villainResponseToBet($amount) {
        $action = $this->villain->actionFacingBet();

        if ($action === "fold") {
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->villain->fold();
            $this->hero->takePot($this->table->getPotSize());
            $this->hero->fold();
            $this->table->cleanTable();
            $this->challenge->incrementHandsPlayed();
            $this->newHand = true;
            //$this->handSetUp();
            return;
        }


        if ($action === "call") {
            $this->villain->call($amount);
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->villain->resetCurrentBet();
            $this->hero->resetCurrentBet();
            $this->allInCheck($this->villain);
            $this->incrementStreet();

            //$this->table->nextStreet();
            return;
        }

        if (($this->hero->isAllin())){
            $this->villain->call($amount);
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->dealer->dealToShowdown();
            $this->showdown();
            return;

        }

        if ($amount >= ($this->villain->getStack() + $this->villain->getCurrentBet()) || $this->hero->isAllin()){
            $this->villain->call($amount);

            $this->allInCheck($this->villain);
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->allInCheck($this->villain);
            $this->newHand = true;
            $this->incrementStreet();
            return;
        }

        $this->villain->raise($amount);
    }
}