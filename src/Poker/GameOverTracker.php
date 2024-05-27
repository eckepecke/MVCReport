<?php

namespace App\Poker;

/**
 * Class GameOverTracker
 *
 * Manages betting logic in a poker game.
 */
class GameOverTracker
{
    private bool $allHandsPlayed = false;
    private int $handsToPlay = 5;

    private int $hand = 0;
    private bool $heroIsBroke = false;


    public function getHandsPlayed(): int
    {
        return $this->handsPlayed;
    }

    public function getGameOver(): bool
    {
        $gameOver = false;
        if ($this->hand >= 6 || $this->heroIsBroke) {
            $gameOver = true;
        }
        return $gameOver;
    }
    public function incrementHands(): void
    {
        $this->hand++;
    }

    public function getHeroIsBroke(): bool
    {
        return $this->heroIsBroke;
    }

    public function checkHeroBroke($stack): void
    {
        if ($stack <= 0) {
            $this->heroIsBroke = true;
        }
    }
}