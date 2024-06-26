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
            $this->nextPosition($player);
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
        usort($players, function ($a, $b) {
            return $a->getPosition() <=> $b->getPosition();
        });

        return $players;
    }

    public function nextPosition($player): void
    {
        $pos = $player->getPosition();
        $pos--;
        if ($pos < 0) {
            $pos = 2;
        }
        $player->setPosition($pos);

    }

    public function playerIsLast(object $player, array $players): bool
    {
        $pos = $player->getPosition();
        $last = true;
        foreach($players as $other) {
            if($other->isActive()) {
                $comparisonPos = $other->getPosition();
                if ($comparisonPos > $pos) {
                    $last = false;
                }
            }

        }
        return $last;
    }
}
