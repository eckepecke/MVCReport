<?php

namespace App\FlopAndGo;

/**
 * A trait implementing histogram for integers.
 */
trait StrategyTrait
{
    public function actionVsCheck(): string
    {
        $options = [
            "check",
            "bet"
        ];

        $decision = $options[rand(0, 1)];
        $this->lastAction = $decision;

        return $decision;
    }

    public function betVsCheck(int $potSize): float
    {
        return 0.75 * $potSize;
    }

    public function randBetSize(int $potSize): float
    {
        $sizes = [
            0.33,
            0.75,
            1.5
        ];

        $size = $sizes[rand(0, 2)];

        return $size * $potSize;
    }

    public function actionFacingBet(): string
    {

        $options = [
            "fold",
            "fold",
            "call",
            "call",
            "call",
            "call",
            "raise"
        ];

        $decision = $options[rand(0, 6)];
        return $decision;
    }


    public function betOpportunity(): string
    {
        $options = [
            "check",
            "bet"
        ];

        $decision = $options[rand(0, 1)];
        return $decision;
    }


}
