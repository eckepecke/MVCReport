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

        public function challengeIsOver() : void 
        {
            echo "is challenge over?";
            $villainStack = $this->villain->getStack();
            $heroStack = $this->hero->getStack();

            if ($this->challenge->challengeComplete($heroStack, $villainStack)) {
                echo"Sätter gomover till true";
                $this->gameOver = true;
            }

            //return $this->challenge->challengeComplete($heroStack, $villainStack);
        }
}