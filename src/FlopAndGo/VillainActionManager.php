<?php

namespace App\FlopAndGo;

/**
 * A trait managing villain opportunities.
 */
trait HeroActionManager
{
    public function villainAction() : void 
    {
        $villainPos = $this->villain->getPosition();
        if ($villainPos === "SB") {
            
        }
    }
}