<?php

namespace App\Poker;

/**
 * Class GameOverTracker
 *
 * Tracks the status of the game.
 */
class GameOverTracker
{
    /** @var bool Indicates whether all hands have been played. */
    private bool $allHandsPlayed;

    /** @var int The total number of hands to play in the game. */
    private int $handsToPlay;

    /** @var int The current hand being played. */
    private int $hand = 0;

    /** @var bool Indicates whether the hero player is broke. */
    private bool $heroIsBroke;

    public function __construct(int $hands)
    {
        $this->allHandsPlayed = false;
        $this->handsToPlay = $hands;
        $this->hand = 0;
        $this->heroIsBroke = false;
    }

    /**
     * Checks if all hands have been played in the game.
     *
     * @return bool True if all hands have been played, false otherwise.
     */
    public function allHandsPlayed(): bool
    {
        if ($this->hand >= $this->handsToPlay) {
            $this->allHandsPlayed = true;
        }
        return $this->allHandsPlayed;
    }

    /**
     * Increments the current hand being played.
     *
     * @return void
     */
    public function incrementHands(): void
    {
        $this->hand++;
    }

    /**
     * Returns hands played.
     *
     * @return int The amount of hands played.
     */
    public function getHandsPlayed(): int
    {
        return $this->hand;
    }
}
