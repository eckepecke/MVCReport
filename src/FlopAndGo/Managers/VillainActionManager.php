<?php

namespace App\FlopAndGo\Managers;

/**
 * A trait managing villain opportunities.
 */
trait VillainActionManager
{
    public function villainPlay($heroAction): void
    {
        $villainPos = $this->gameProperties['villain']->getPosition();

        if (($heroAction !== "check") && ($villainPos === "SB")) {
            // Villain nedds to wait his turn
            return;
        }
        $action = $this->gameProperties['villain']->betOpportunity();

        if ($villainPos === "SB") {
            $this->villainPlayIP($action);
            return;
        }

        $this->villainPlayOOP($action);
    }

    public function villainPlayIP(string $action)
    {
    if ($action === "check") {
        $this->gameProperties['villain']->check();
        echo"check IP inc";
        $this->incrementStreet();
        return;
    }
    $this->villainBet();
    }


    public function villainPlayOOP(string $action)
    {
        if ($action === "check") {
            $this->gameProperties['villain']->check();
            return;
        }
        $this->villainBet();
    }

    public function villainBet()
    {
    $betSize = $this->gameProperties['villain']->randBetSize($this->gameProperties['table']->getPotSize());
    if ($betSize > $this->gameProperties['hero']->getStack()) {
        $betSize = $this->gameProperties['hero']->getStack();
    }
    $this->gameProperties['villain']->bet($betSize);
    }
}
