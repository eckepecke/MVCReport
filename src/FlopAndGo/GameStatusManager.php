<?php

namespace App\FlopAndGo;

/**
 * A trait managing card and chip distribution.
 */
trait GameStatusManager
{
    public function isShowdown() : bool 
        {
            echo "isShowdown";
            var_dump($this->streetCheck());
            return ($this->streetCheck() === 4);
        }

        public function challengeIsOver() : bool 
        {
            $villainStack = $this->villain->getStack();
            $heroStack = $this->hero->getStack();

            if ($this->challenge->challengeComplete($heroStack, $villainStack)) {
                $this->gameOver = true;
            }

            return $this->challenge->challengeComplete($heroStack, $villainStack);
        }
}