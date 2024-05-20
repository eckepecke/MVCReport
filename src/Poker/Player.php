<?php

namespace App\Poker;

use App\Cards\CardHand;

class Player
{
    protected int $stack;
    protected ?object $hand;
    protected int $position;
    protected int $currentBet;
    protected string $lastAction;
    protected bool $allIn;


    public function __construct()
    {
        $this->stack = 5000;
        $this->hand = null;
        $this->position = 0;
        $this->currentBet = 0;
        $this->lastAction = "";
        $this->allIn = false;
    }

    public function addHand(CardHand $hand): void
    {
        $this->hand = $hand;
    }


    public function getHand(): object
    {
        return $this->hand;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function nextPosition(): void
    {
        $this->position++;
        if ($this->position > 3) {
            $this->position = 1;
        }
    }

    public function getCurrentBet(): int
    {
        return $this->currentBet;
    }
}