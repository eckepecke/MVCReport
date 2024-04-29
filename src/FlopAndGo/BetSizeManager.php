<?php

namespace App\FlopAndGo;

trait BetSizeManager
{
    public function getMaxBet($player1, $player2) : int
    {
        $player1StackAndBet = ($player1->getStack() + $player1->getCurrentBet());
        $player2StackAndBet = ($player2->getStack()+ $player2->getCurrentBet());
        var_dump($player1StackAndBet);
        var_dump($player2StackAndBet);

        $maxBet = min($player1StackAndBet, $player2StackAndBet);

        return $maxBet;
    }
}