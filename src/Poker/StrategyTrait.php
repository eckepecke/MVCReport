<?php

namespace App\Poker;

/**
 * A trait implementing histogram for integers.
 */
trait StrategyTrait
{
    public function decisionFacingLimp() : string
    {
        $options = [
            "check",
            "raise"
        ];

        $decision = $options[rand(0, 1)];
        return $decision;
    }
}