<?php

namespace App\FlopAndGo;

/**
 * A trait implementing histogram for integers.
 */
trait StrategyTrait
{
    // public function randActionRFI(): string
    // {
    //     $decision = "preflopRaise";

    //     $randNum = rand(0, 10);
    //     if ($randNum < 2) {
    //         $decision = "preflopCall";
    //     }
    //     return $decision;
    // }


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

    public function postFlopBetOpportunity(): string
    {
        if (in_array($this->lastAction, ["raise", "bet"])) {
            $options = [
                "check",
                "bet",
                "bet",
                "bet",
            ];
            $decision = $options[rand(0, 3)];
            return $decision;
        }

        $options = [

            "check",
            "check",
            "check",
            "bet",
        ];

        //$decision = $options[rand(0, 1)];
        $decision = $options[rand(0, 3)];
        $this->lastAction = $decision;
        return $decision;
    }

    public function betOpportunity(): string
    {
        $options = [
            "check",
            "bet"
        ];

        $decision = $options[rand(0, 1)];
        $this->lastAction = $decision;

        return $decision;
    }


}
