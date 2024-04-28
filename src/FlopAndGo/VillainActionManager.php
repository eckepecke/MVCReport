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
        $this->villain->bet($this->table->getPotSize());
        
        }

        if ($villainPos === "SB") {
            if ($action === "check") {
                $this->villain->check();
                return;
            }
        $this->villain->bet($this->table->getPotSize());
        }

        $this->table->addChipsToPot($this->villain->getCurrentBet());
    }
}