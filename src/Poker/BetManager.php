<?php

namespace App\Poker;

/**
 * Class BetManager
 *
 * Manages betting logic in a poker game.
 */
class BetManager
{
    private bool $actionIsClosed = false;

    public function getActionIsClosed(): bool
    {
        return $this->actionIsClosed;
    }

    public function setActionIsClosed(bool $actionIsClosed): void
    {
        $this->actionIsClosed = $actionIsClosed;
    }
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
        echo"playerClosedAction()";
        $playerClosedAction = false;

        $playerLastAction = $player->getLastAction();
        $priceToPlay = $this->getPriceToPlay($state);
        // $playerPos = $player->getPosition();
        $activePlayers = $state["active"];
        // $lastToActIndex = count($activePlayers) - 1;
        $lastToAct = $this->LastToAct($state["players"]);
        var_dump($priceToPlay);
        var_dump($playerLastAction);
        echo"lastToAct:";
        var_dump($lastToAct->getName());
        var_dump($lastToAct->getPosition());



        // var_dump($playerPos);
        if ($priceToPlay === 0 && $activePlayers === 2) {
            echo"AAA";

            $playerClosedAction = true;
        }

        if ($playerLastAction === "check" && $player === $lastToAct) {
            echo"CCC";

            $playerClosedAction = true;
        }
        // }
        
        // This is true for both preflop and postflop.
        if ($playerLastAction === "call" && $priceToPlay === 0) {
            echo"DDD";

            $playerClosedAction = true;
        }

        if ($playerLastAction === "fold" && $priceToPlay === 0) {
            echo"FFF";

            $playerClosedAction = true;
        }

        return $playerClosedAction;
        }

    public function LastToAct( array $players): object
    {
        $last = null;
        $biggestNumber = -1;
        foreach ($players as $player) {
            if ($player->isActive()) {
                $pos = $player->getPosition();
                if ($pos > $biggestNumber) {
                    $biggestNumber = $pos;
                    $last = $player;
                }
            }
        }
        return $last;
    }
    }