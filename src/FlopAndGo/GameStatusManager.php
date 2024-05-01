<?php

namespace App\FlopAndGo;

/**
 * A trait managing card and chip distribution.
 */
trait GameStatusManager
{
    public function isShowdown() : bool 
        {
            return ($this->streetCheck() === 4);
        }

        public function isAllHandsPlayed() : bool 
        {
            return $this->challenge->challengeComplete();
        }

        public function isSomeoneBroke() {
            $heroStack = $this->hero->getStack();
            $villainStack = $this->villain->getStack();

            $broke = false;
            if ($heroStack <= 0 || $villainStack <= 0) {
                $broke = true;
            }
            return $broke;
        }
    
}