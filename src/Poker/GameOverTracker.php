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
    private int $hand;
    private bool $heroIsBroke;

    public function __construct($hands) 
    {
        $this->allHandsPlayed = false;
        $this->handsToPlay = $hands;
        $this->hand = 0;
        $this->heroIsBroke = false;
    }



    public function getHandsPlayed(): int
    {
        return $this->handsPlayed;
    }

    public function getGameOver(): bool
    {
        $gameOver = false;
        var_dump($this->hand);
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
        var_dump($stack);
        if ($stack <= 0) {
            $this->heroIsBroke = true;
        }
    }
}