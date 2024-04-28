<?php

namespace App\FlopAndGo;

/**
 * A trait managing card and chip distribution.
 */
trait GameStatusManager
{
    public function isShowdown() :bool 

        {
            echo "isShowdown";
            var_dump($this->streetCheck());
            return ($this->streetCheck() === 4);
        }

        public function isChallengeOver() {
            
        }
}