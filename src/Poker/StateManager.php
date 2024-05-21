<?php

namespace App\Poker;

use Exception;

/**
 * Class StateManagerManager
 * 
 * Manages state variables in a poker game.
 */
class StateManager
{
    public function getActivePlayers(array $state): int
    {
        $players = $state["players"];
        $count = 0;
        foreach($players as $player) {
            if ($player->isActive()) {
                $count++;
            }
        }

        return $count;
    }

    public function getWinner(array $state): object
    {
        $players = $state["players"];
        foreach($players as $player) {
            if ($player->isActive()) {
                return $player;
            }
        }

        throw new Exception("No active player!");
    }
}