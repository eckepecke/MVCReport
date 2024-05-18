<?php

namespace App\FlopAndGo\Managers;

/**
 * A trait managing card and chip distribution.
 */
trait GameStatusManager
{
    public function isShowdown(): bool
    {
        return ($this->streetCheck() === 4);
    }

    public function isAllHandsPlayed(): bool
    {
        return $this->gameProperties['challenge']->challengeComplete();
    }

    public function isSomeoneBroke(): bool
    {
        $heroStack = $this->gameProperties['hero']->getStack();
        $villainStack = $this->gameProperties['villain']->getStack();

        $broke = false;
        if ($heroStack <= 0 || $villainStack <= 0) {
            $broke = true;
        }
        return $broke;
    }

}
