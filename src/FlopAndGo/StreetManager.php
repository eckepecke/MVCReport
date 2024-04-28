<?php

namespace App\FlopAndGo;

/**
 * A trait managing card and chip distribution.
 */
trait StreetManager
{
    public function handSetUp() : void
    {
        $this->table->moveButton();
        $this->table->getBombPotChips();
        $this->dealer->dealHoleCards();
        $this->newHand = false;
    }
}
