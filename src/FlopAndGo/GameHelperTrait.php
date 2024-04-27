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
}