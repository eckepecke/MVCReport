<?php

namespace App\Poker;

/**
 * A trait implementing histogram for integers.
 */
trait StrategyTrait
{
    public function randActionRFI(): string
    {
        $options = [
            "preflopCall",
            "preflopRaise",
            "fold"
        ];

        $decision = $options[rand(0, 2)];
        return $decision;
    }


    public function actionVsCheck(): string
    {
        $options = [
            "check",
            "bet"
        ];

        //$decision = $options[rand(0, 1)];
        $decision = $options[1];

        return $decision;
    }

    public function betVsCheck($potSize): float
    {
        return 0.75 * $potSize;
    }

    public function actionFacingBet(): string
    {
        // if $this->hand === "AA"{
        //     return "raise"
        // }
        $options = [
            "fold",
            "call",
            "raise"
        ];

        //$decision = $options[rand(0, 1)];
        $decision = $options[rand(0, 2)];

        return $decision;
    }

}
