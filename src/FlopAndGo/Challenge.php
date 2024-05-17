<?php

namespace App\FlopAndGo;

use App\FlopAndGo\Hero;
use App\FlopAndGo\Villain;

/**
 * Tracks hands played and handwinner.
 */
class Challenge
{
    /**
     * @var int The duration of the challenge in number of hands.
     */
    private int $duration;
    /**
     * @var int The number of hands played in the challenge.
     */
    private int $handsPlayed;
    /**
     * @var string The winner of the current hand in the challenge.
     */
    private string $handWinner;

    /**
     * Challenge constructor.
     *
     * @param int $hands The number of hands in the challenge. Defaults to 2.
     */
    public function __construct(int $hands = 2)
    {
        $this->duration = $hands;
        $this->handsPlayed = 0;
        $this->handWinner = "";
    }

    /**
     * Increment the number of hands played in the challenge.
     */
    public function incrementHandsPlayed(): void
    {
        $this->handsPlayed += 1;
    }

    /**
     * Check if the challenge is complete.
     *
     * @return bool True if the challenge is complete, false otherwise.
     */
    public function challengeComplete(): bool
    {
        $done = false;
        if ($this->handsPlayed >= $this->duration) {
            $done = true;
        }
        return $done;
    }

    /**
     * Get the duration of the challenge.
     *
     * @return int The duration of the challenge in number of hands.
     */
    public function getDuration(): int
    {
        return $this->duration;
    }


    /**
     * Get the number of hands played in the challenge.
     *
     * @return int The number of hands played.
     */
    public function getHandsPlayed(): int
    {
        return $this->handsPlayed;
    }


    /**
     * Get the result of the challenge.
     *
     * @param int $startingStack The starting stack.
     * @param int $currentStack The current stack.
     * @return int The result of the challenge.
     */
    public function getResult(int $startingStack, int $currentStack): int
    {
        return ($currentStack - $startingStack);
    }

    /**
     * Get the winner of the current hand in the challenge.
     *
     * @return string The winner of the current hand.
     */
    public function getHandWinner(): string
    {
        return $this->handWinner;
    }

    /**
     * Set the winner of the current hand in the challenge.
     *
     * @param string $handWinner The winner of the current hand.
     */
    public function setHandWinner(string $handWinner): void
    {
        $this->handWinner = $handWinner;
    }
}
