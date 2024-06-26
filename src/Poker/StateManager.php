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
    private bool $everyoneMoved = false;
    private bool $newHand = true;


    public function getNewHand(): bool
    {
        return $this->newHand;
    }

    public function setNewHand(bool $bool): void
    {
        $this->newHand = $bool;
    }

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

    public function removeInactive(array $players): array
    {

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

    public function heroAlreadyMoved($heroAction): bool
    {
        $heroAlreadyMoved = false;
        if ($heroAction != "next" && $heroAction != null) {
            $heroAlreadyMoved = true;
        }

        return $heroAlreadyMoved;
    }

    public function wonWithNoShowdown(array $state): void
    {
        $activePlayers = $this->getActivePlayers($state);
        if ($activePlayers < 2) {
            $this->newHand = true;
        }
    }
}
