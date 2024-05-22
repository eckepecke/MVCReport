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

    public function removeInactive(array $state): array
    {
        $players = $state["players"];

        $index = 0;
        foreach($players as $player) {
            $keep = $player->isActive();
            if (!$keep) {
                unset($players[$index]);
            }
            $index++;
        }
        return array_values($players);
    }
}