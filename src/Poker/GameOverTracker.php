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

    public function __construct($hands)
    {
        $this->allHandsPlayed = false;
        $this->handsToPlay = $hands;
        $this->hand = 0;
        $this->heroIsBroke = false;
        $this->gameOver = false;

    }



    public function allHandsPlayed(): int
    {
        if ($this->hand >= $this->handsToPlay) {
            echo"2";
            $this->allHandsPlayed = true;
        }
        return $this->allHandsPlayed;
    }

    public function getGameOver(): int
    {
        return $this->gameOver;
    }


    public function incrementHands(): void
    {
        $this->hand++;
    }

    public function getHeroIsBroke(): bool
    {
        return $this->heroIsBroke;
    }

    public function checkHeroBroke($stack): bool
    {
        var_dump($stack);
        if ($stack <= 0) {
            $this->heroIsBroke = true;
        }

        return $this->heroIsBroke;
    }
}
