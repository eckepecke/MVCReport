<?php

namespace App\FlopAndGo;

trait BetSizeManager
{
    public function getMaxBet(object $player1, object $player2): int
    {
        $player1StackAndBet = ($player1->getStack() + $player1->getCurrentBet());
        $player2StackAndBet = ($player2->getStack() + $player2->getCurrentBet());

        $maxBet = min($player1StackAndBet, $player2StackAndBet);

        return $maxBet;
    }
}
