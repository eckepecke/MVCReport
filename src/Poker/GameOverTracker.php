<?php

namespace App\Poker;

/**
 * Class GameOverTracker
 *
 * Manages betting logic in a poker game.
 */
class GameOverTracker
{
    private bool $allHandsPlayed;
    private int $handsToPlay;
    private int $hand = 0;
    private bool $heroIsBroke;

    public function __construct(int $hands)
    {
        $this->allHandsPlayed = false;
        $this->handsToPlay = $hands;
        $this->hand = 0;
        $this->heroIsBroke = false;
        $this->gameOver = false;
    }

    public function allHandsPlayed(): bool
    {
        if ($this->hand >= $this->handsToPlay) {
            echo"2";
            $this->allHandsPlayed = true;
        }
        return $this->allHandsPlayed;
    }

    public function incrementHands(): void
    {
        $this->hand++;
    }

    public function getHeroIsBroke(): bool
    {
        return $this->heroIsBroke;
    }
}
