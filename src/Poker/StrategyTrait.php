<?php

namespace App\Poker;

/**
 * A trait implementing histogram for integers.
 */
trait StrategyTrait
{
    public function randActionRFI(): string
    {
        $decision = "preflopRaise";

        $randNum = rand(0, 10);
        if ($randNum < 2){
            $decision = "preflopCall";
        }
        return $decision;
    }


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
        $this->lastAction = $decision;

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

}
