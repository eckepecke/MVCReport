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
        var_dump($action);
        $action = "check";

        if ($villainPos === "BB") {
            if ($action === "check") {
                $this->villain->check();
                return;
            }
        }
            // if ($villainBet > $this->hero->getStack()) {
            //     $villainBet = $villainBet - $this->hero->getStack();
            // }


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
}