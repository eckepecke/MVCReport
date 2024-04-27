<?php

namespace App\Poker;

use App\Poker\Hero;
use App\Poker\Villain;

class Challenge
{
    private int $duration;
    private object $villain;
    private object $hero;
    private object $table;
    private object $dealer;
    private string $handWinner;


    private int $handsPlayed;

    public function __construct(int $hands)
    {
        $this->duration = $hands;
        $this->handsPlayed = 0;
        $this->handWinner = "";
    }

    public function addVillain(Villain $villain): void
    {
        $this->villain = $villain;
    }

    public function addHero(Hero $hero): void
    {
        $this->hero = $hero;
    }

    public function addTable(Table $table): void
    {
        $this->table = $table;
    }

    public function addDealer(Dealer $dealer): void
    {
        $this->dealer = $dealer;
    }

    public function incrementHandsPlayed(): void
    {
        $this->handsPlayed += 1;
    }

    public function challengeComplete(): bool
    {
        $done = false;
        $p1Stack = $this->hero->getStack();
        $p2Stack = $this->villain->getStack();

        if ($p1Stack <= 0 || $p2Stack <= 0 || $this->handsPlayed >= $this->duration) {
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
