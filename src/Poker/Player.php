<?php

namespace App\Poker;

use App\Poker\CardHand;

class Player extends PlayerBase
{
    protected int $stack;
    protected ?object $hand;
    protected int $position;
    protected int $currentBet;
    protected string $lastAction;
    protected bool $allIn;
    protected bool $isHero;
    protected bool $active;
    protected string $name;

    public function __construct()
    {
        parent::__construct();
        $this->position = 0;
        $this->allIn = false;
        $this->isHero = false;
        $this->active = true;
        $this->name = "";
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getPositionString(): string
    {
        $posArray = [
            0 => "SB",
            1 => "BB",
            2 => "BTN"
        ];

        return $posArray[$this->position];
    }

    public function payBlind(int $blind): void
    {
        $this->stack -= $blind;
        $this->currentBet = $blind;
    }

    public function isHero(): bool
    {
        return $this->isHero;
    }

    public function setHero(): void
    {
        $this->isHero = true;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deActivate(): void
    {
        $this->active = false;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function takePot(int $chips): void
    {
        $this->stack += $chips;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
