<?php

namespace App\Poker;

use App\Poker\Hero;
use App\Poker\Villain;

class Challenge
{
    private $duration;
    private $villain;
    private $hero;
    private $handsPlayed;

    public function __construct(int $hands)
    {
        $this->duration = $hands;
        $this->handsPlayed = 0;
    }

    public function addVillain(Villain $villain): void
    {
        $this->villain = $villain;
    }

    public function addHero(Hero $hero): void
    {
        $this->hero = $hero;
    }

    public function incrementHandsPlayed(): void
    {
        $this->handsPlayed += 1;
    }

    public function challengeComplete(): bool
    {
        return $this->handsPlayed === $this->duration;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getHandsPlayed(): int
    {
        return $this->handsPlayed;
    }

    public function getHeroName(): string
    {
        return $this->hero->getName();
    }

    public function getVillainName(): string
    {
        return $this->villain->getName();
    }
}
