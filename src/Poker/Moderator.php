<?php

namespace App\Poker;

class Moderator
{
    private $handsPlayed;

    public function __construct()
    {
        $this-> $handsPlayed = 0;
    }

    public function count(): void
    {
        $this->handsPlayed += 1;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function addVillain(Villain $villain): void
    {
        $this->villain = $villain;
    }

    public function addHero(Hero $hero): void
    {
        $this->hero = $hero;
    }
}
