<?php

namespace App\FlopAndGo\Managers;

/**
 * A trait managing card and chip distribution.
 */
trait GameStatusManager
{


    public function isAllHandsPlayed(): bool
    {
        return $this->gameProperties['challenge']->challengeComplete();
    }

    public function isSomeoneBroke(): void
    {
        $heroStack = $this->gameProperties['hero']->getStack();
        $villainStack = $this->gameProperties['villain']->getStack();

        $broke = false;
        if ($heroStack <= 0 || $villainStack <= 0) {
            $broke = true;
        }


        $this->gameOver = $broke;
    }

}
