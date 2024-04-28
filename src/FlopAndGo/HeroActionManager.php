<?php

namespace App\FlopAndGo;

/**
 * A trait managing user input.
 */
trait HeroActionManager
{
    public function heroAction(mixed $action) : void 
    {
        if ($action != null && $action != "next"){
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

    public function heroFolded() : void 
    {
        echo"hero folded";
        $this->table->addChipsToPot($this->villain->getCurrentBet());
        $this->table->addChipsToPot($this->hero->getCurrentBet());
        $this->hero->fold();
        $this->villain->takePot($this->table->getPotSize());
        $this->villain->fold();
        $this->table->cleanTable();
        $this->challenge->incrementHandsPlayed();
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
        $this->incrementStreet();
    }

    public function heroBet(int $amount) :void
    {

        $betSize= $this->heroBetSize($amount);

        $this->hero->bet($betSize);
        $action = $this->villain->actionFacingBet();
        //var_dump($action);
        //$action = "call";
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


            // might add strret variable here
            return;
        }

        if ($action === "call") {
            $this->villain->call($amount);

            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->villain->resetCurrentBet();
            $this->hero->resetCurrentBet();
            $this->incrementStreet();

            //$this->table->nextStreet();
            return;
        }

        if ($amount >= ($this->villain->getStack() + $this->villain->getCurrentBet())){
            $this->villain->call($amount);
            $this->table->addChipsToPot($this->hero->getCurrentBet());
            $this->table->addChipsToPot($this->villain->getCurrentBet());
            $this->newHand = true;
            $this->incrementStreet();

            //$this->handSetUp();
            return;
        }
        $this->villain->raise($amount);
    }

    public function heroChecked() : void 
    {
        $this->hero->check();
        if ($this->hero->getPosition() === "SB") {
            $this->incrementStreet();
        }
    }

    public function heroBetSize(int $amount) : int 
    {
        $villainStack = $this->villain->getStack();
        $villainCurrentBet = $this->villain->getCurrentBet();
        if ($amount > ($villainStack + $villainCurrentBet)){
            $amount = $villainStack + $villainCurrentBet;
        }
        return $amount;
    }
}