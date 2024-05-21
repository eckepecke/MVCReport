<?php

namespace App\Poker;

/**
 * Class PositionManager
 * 
 * Manages the positions of players in a poker game.
 */
class PositionManager
{
    /**
     * Assigns positions to players.
     *
     * @param array $players The array of Player objects.
     * 
     * @return void
     */
    public function assignPositions(array $players): void
    {   
        $position = 0;
        foreach ($players as $player) {
            $player->setPosition($position);
            $position++;
        }
    }

    /**
     * Updates positions of players to the next position.
     *
     * @param array $players The array of Player objects.
     * 
     * @return void
     */
    public function updatePositions(array $players): void
    {   
        foreach ($players as $player) {
            $player->nextPosition();
        }
    }


    /**
     * Arrange player array based on lowest position value
     * 
     * @param array $players The array of Player objects.
     * 
     * @return array
     */
    public function sortPlayersByPosition(array $players): array
    {
        $tempArray = [0,1,2];
        foreach ($players as $player) {
            $value = $player->getPosition();
            $tempArray[$value] = $player;
        }

        return $tempArray;
    }
}
