<?php

namespace App\Poker;

/**
 * Class Opponent
 *
 * This class represents an opponent in a poker game. It inherits from the Player class.
 *
 */
class Opponent extends Player
{
    public function __construct()
    {
        parent::__construct();
        $this->stack = 50000;
    }

    public function responseToBet()
    {
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
        $options = [
            "check",
            "bet",
        ];

        $decision = $options[rand(0, 1)];
        return $decision;
    }


    public function chooseBetSize($potSize): float
    {
        if ($potSize === 0) {
            return 500;
        }
        return 0.75 * $potSize;
    }

    public function actionVsShove(): string
    {
        $options = [
            "fold",
            "call",
        ];

        $decision = $options[rand(0, 1)];
        return $decision;
    }
}
