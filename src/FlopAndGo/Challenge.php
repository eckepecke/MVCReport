<?php

namespace App\FlopAndGo;

use App\FlopAndGo\Hero;
use App\FlopAndGo\Villain;

class Challenge
{
    private int $duration;
    private int $handsPlayed;
    private string $handWinner;

    public function __construct(int $hands)
    {
        $this->duration = $hands;
        $this->handsPlayed = 0;
        $this->handWinner = "";
    }

    public function incrementHandsPlayed(): void
    {
        $this->handsPlayed += 1;
    }

    public function challengeComplete(): bool
    {
        $done = false;
        if ($this->handsPlayed >= $this->duration) {
            $done = true;
        }
        return $done;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getHandsPlayed(): int
    {
        return $this->handsPlayed;
    }

    public function getResult(int $startingStack, int $currentStack): int
    {
        return ($currentStack - $startingStack);
    }

    public function getHandWinner(): string
    {
        return $this->handWinner;
    }

    public function setHandWinner(string $handWinner): void
    {
        $this->handWinner = $handWinner;
    }
}
