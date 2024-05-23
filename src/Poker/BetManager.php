<?php

namespace App\Poker;

/**
 * Class BetManager
 *
 * Manages betting logic in a poker game.
 */
class BetManager
{
    /**
     * Gets the price to play for the current betting round.
     *
     * @return int The difference between the highest and lowest current bets.
     */
    public function getPriceToPlay(array $state): int
    {
        $players = $state["players"];
        $activeBetsArray = [];
        foreach ($players as $player) {

            if ($player->isActive()) {
                $activeBetsArray[] = $player->getCurrentBet();
            }
        }

        $biggestAmount = max($activeBetsArray);
        $smallestAmount = min($activeBetsArray);

        return $biggestAmount - $smallestAmount;
    }

    /**
     * Gets the biggest current bet.
     *
     * @return int The biggest current bet.
     */
    public function getBiggestBet(array $state): int
    {
        $players = $state["players"];
        $betsArray = [];
        foreach ($players as $player) {
            $betsArray[] = $player->getCurrentBet();
        }

        $biggestBet = max($betsArray);

        return $biggestBet;
    }

    /**
     * Gets the minimum raise allowed for the current betting round.
     *
     * @return int The minimum amount required to raise.
     */
    public function getMinimumRaiseAllowed(array $state): int
    {
        $players = $state["players"];
        $amountOne = $players[0]->getCurrentBet();
        $amountTwo = $players[1]->getCurrentBet();
        $amountThree = $players[2]->getCurrentBet();

        $biggestAmount = max($amountOne, $amountTwo, $amountThree);
        // If no bets have been placed, min bet = 50
        $biggestAmount = max($biggestAmount, 25);
        return 2 * $biggestAmount;
    }

    public function resetPlayerBets(array $players): void
    {
        foreach ($players as $player) {
            $player->resetCurrentBet();
        }
    }

    public function resetPlayerActions(array $players): void
    {
        foreach ($players as $player) {
            $player->resetLastAction();
        }
    }

    public function playerClosedAction(object $player, array $state): bool
    {
        $playerClosedAction = false;


        $playerLastAction = $player->getLastAction();

        $priceToPlay = $this->getPriceToPlay($state);

        if ($playerLastAction === "call" && $priceToPlay === 0) {

            $playerClosedAction = true;
        }


        return $playerClosedAction;
    }

    public function playerClosedActionPreflop(object $player, array $state): bool
    {
        $playerClosedAction = false;


        $playerLastAction = $player->getLastAction();
        $playerPos = $player->getLastAction();


        $priceToPlay = $this->getPriceToPlay($state);

        if ($playerLastAction === "call" && $priceToPlay === 0) {
            $playerClosedAction = true;
        }

        // If big blind check backs action is closed despit price not 0.
        if ($playerLastAction === "check" && $playerPos === 1) {
            $playerClosedAction = true;
        }


        return $playerClosedAction;
    }
}
