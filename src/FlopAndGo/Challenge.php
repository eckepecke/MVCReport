<?php

namespace App\FlopAndGo;

use App\Poker\Hero;
use App\Poker\Villain;

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

    public function challengeComplete(int $stack1, int $stack2): bool
    {
        $done = false;
        if ($stack1 <= 0 || $stack2 <= 0 || $this->handsPlayed >= $this->duration) {
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

    public function setHandWinner($handWinner): void
    {
        $this->handWinner = $handWinner;
    }
}
