<?php

namespace App\FlopAndGo\Managers;

/**
 * A trait managing villain opportunities.
 */
trait VillainActionManager
{
    public function villainPlay($heroAction): void
    {
        $villainPos = $this->villain->getPosition();

        if (($heroAction === null) && ($villainPos === "SB")) {
            // Villain nedds to wait his turn
            return;
        }
        $action = $this->villain->betOpportunity();

        if ($villainPos === "SB") {
            $this->villainPlayIP($action);
            return;
        }

        $this->villainPlayOOP($action);
    }

    public function villainPlayIP(string $action)
    {
    if ($action === "check") {
        $this->villain->check();
        $this->incrementStreet();
        return;
    }
    $this->villainBet();
    }


    public function villainPlayOOP(string $action)
    {
        if ($action === "check") {
            $this->villain->check();
            return;
        }
        $this->villainBet();
    }

    public function villainBet()
    {

    $betSize = $this->villain->randBetSize($this->table->getPotSize());
    if ($betSize > $this->hero->getStack()) {
        $betSize = $this->hero->getStack();
    }
    $this->villain->bet($betSize);
    }
}
