<?php

namespace App\Poker;

/**
 * A trait implementing histogram for integers.
 */
trait StrategyTrait
{
    public function randActionRFI() : string
    {
        $options = [
            "preflopCall",
            "preflopRaise",
            "fold"
        ];

        $decision = $options[rand(0, 2)];
        return $decision;
    }

}