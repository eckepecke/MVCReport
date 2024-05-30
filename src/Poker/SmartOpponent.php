<?php

namespace App\Poker;

class SmartOpponent extends Player
{
    public function __construct()
    {
        parent::__construct();
        $this->stack = 50000;
    }

    public function responseToBet()
    {
        echo"SMART";

        $strength = $this->hand->getStrengthInt();
        if($strength > 1) {
            $decision = "raise";
            echo"hehe";

            return $decision;
        }


        $options = [
            "fold",
            "call",
            "call",
            "call",
            "raise",
            "raise"
        ];

        $decision = $options[rand(0, 5)];
        return $decision;
    }


    public function actionVsCheck()
    {
        echo"SMART";

        $strength = $this->hand->getStrengthInt();
        if($strength > 1) {
            $decision = "bet";
            $options = [
                "check",
                "bet",
                "bet",
                "bet",
            ];
            $decision = $options[rand(0, 3)];
            echo"hehe";
            return $decision;
        }

        $options = [
            "check",
            "bet",
        ];

        $decision = $options[rand(0, 1)];
        return $decision;
    }


    public function chooseBetSize($potSize): float
    {
        return 0.75 * $potSize;
    }

    public function actionVsShove(): string
    {
        echo"SMART";
        $strength = $this->hand->getStrengthInt();
        if($strength > 1) {
            $decision = "call";
            echo"hehe";
            return $decision;
        }
        $options = [
            "fold",
            "fold",
            "fold",
            "call",
        ];

        $decision = $options[rand(0, 3)];
        return $decision;
    }
}
