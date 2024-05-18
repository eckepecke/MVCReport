<?php

namespace App\FlopAndGo\Managers;

/**
 * A trait managing villain opportunities.
 */
trait VillainActionManager
{
    public function villainPlay($heroAction): void
    {
        echo "villainplay()";
        echo "heroAction:";
        var_dump($heroAction);
        $villainPos = $this->villain->getPosition();

        if (($heroAction !== "check") && ($villainPos === "SB")) {
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
    echo "villainplay IP()";

    if ($action === "check") {
        $this->villain->check();
        echo"check IP inc";
        $this->incrementStreet();
        return;
    }
    $this->villainBet();
    }


    public function villainPlayOOP(string $action)
    {
        echo "villainplay OOP()";
        if ($action === "check") {
            $this->villain->check();
            return;
        }
        $this->villainBet();
    }

    public function villainBet()
    {
    echo "villainBet()";
    $betSize = $this->villain->randBetSize($this->table->getPotSize());
    if ($betSize > $this->hero->getStack()) {
        $betSize = $this->hero->getStack();
    }
    $this->villain->bet($betSize);
    }
}
