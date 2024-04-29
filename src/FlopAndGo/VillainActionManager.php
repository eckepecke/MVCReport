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
                $this->villain->check();
                return;
            }
        }

        if ($villainPos === "SB") {
            if ($action === "check") {
                $this->villain->check();
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
        echo "villainResonsetoBet()";
        $action = $this->villain->actionFacingBet();
        $action = "raise";

        if ($action === "fold") {
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->villain->fold();
            $this->hero->takePot($this->table->getPotSize());
            $this->hero->fold();
            $this->table->cleanTable();
            $this->challenge->incrementHandsPlayed();
            $this->newHand = true;
            $this->handSetUp();
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
        echo "hero is allin?";

            $this->villain->call($amount);
            var_dump($amount);
            var_dump($this->table->getPotSize());

            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            var_dump($this->table->getPotSize());

            $this->dealer->dealToShowdown();
            $this->showdown();
            return;

        }
        var_dump($this->hero->isAllin());
        if ($amount >= ($this->villain->getStack() + $this->villain->getCurrentBet()) || $this->hero->isAllin()){
            echo "do Iget here?";
            $this->villain->call($amount);
            $this->allInCheck($this->villain);
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->allInCheck($this->villain);
            /// should I call showdown here
            $this->newHand = true;
            $this->incrementStreet();

            //$this->handSetUp();
            return;
        }
        echo "end raise triggered";
        $this->villain->raise($amount);
    }
}