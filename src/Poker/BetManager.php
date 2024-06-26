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
     * @param array{players: Player[]} $state
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

    /**
     * Resets the current bet for each player in the given array.
     *
     * @param array $players An array of player objects with a resetCurrentBet method.
     * @return void
     */
    public function resetPlayerBets(array $players): void
    {
        foreach ($players as $player) {
            $player->resetCurrentBet();
        }
    }

    /**
     * Resets the current all in status back to false for each player in the given array.
     *
     * @param array $players An array of player objects with a resetCurrentBet method.
     * @return void
     */
    public function resetPlayersAllIn(array $players): void
    {
        foreach ($players as $player) {
            $player->resetAllIn();
        }
    }

    /**
     * Resets the last action for each player in the given array.
     *
     * @param array $players An array of player objects with a resetLastAction method.
     * @return void
     */
    public function resetPlayerActions(array $players): void
    {
        foreach ($players as $player) {
            $player->resetLastAction();
        }
    }

    /**
     * Determines if a player's action has closed the current round of betting.
     *
     * @param object $player The player object with methods to get their last action.
     * @param array $state The current game state, including active players and other details.
     * @return bool True if the player's action closed the round, false otherwise.
     */
    public function playerClosedAction(object $player, array $state): bool
    {
        $playerClosedAction = false;
        $street = $state["street"];
        $playerLastAction = $player->getLastAction();
        $playerBet = $player->getCurrentBet();
        $priceToPlay = $this->getPriceToPlay($state);
        $allIn = $player->isAllIn();

        $lastToAct = $this->lastToAct($state["players"]);

        if ($playerLastAction === "check" && $player === $lastToAct) {
            $playerClosedAction = true;
        }
        //This is for preflop
        if ($playerLastAction === "call" && $priceToPlay === 0 && $street === "preflop"  && $playerBet > 400) {
            $playerClosedAction = true;
        }
        // this is for postflop
        if ($playerLastAction === "call" && $priceToPlay === 0 && $street != "preflop") {
            $playerClosedAction = true;
        }

        if ($playerLastAction === "fold" && $priceToPlay === 0) {
            $playerClosedAction = true;
            if ($street === "preflop" && $playerBet === 200) {
                $playerClosedAction = false;
            }
        }

        if ($playerLastAction === "call" && $allIn) {
            $playerClosedAction = true;
        }

        return $playerClosedAction;
    }

    /**
     * Determines the last active player to act based on their position.
     *
     * @param array $players An array of player objects with methods to check activity and get position.
     * @return object The last active player to act.
     */
    public function lastToAct(array $players): object
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
