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
    protected bool $isHero;
    protected bool $active;


    public function __construct()
    {
        $this->stack = 5000;
        $this->hand = null;
        $this->position = 0;
        $this->currentBet = 0;
        $this->lastAction = "";
        $this->allIn = false;
        $this->isHero = false;
        $this->active = true;
    }

    public function addHand(CardHand $hand): void
    {
        $this->hand = $hand;
    }


    public function getHand(): ?object
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

    public function getPositionString(): string
    {
        $posArray = [
            0 => "SB",
            1 => "BB",
            2 => "BTN"
        ];

        return $posArray[$this->position];
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

    public function resetCurrentBet(): void
    {
        $this->currentBet = 0;
    }

    public function getStack(): int
    {

        return $this->stack;
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
        $this->active = $true;
    }

    public function deActivate(): void
    {
        $this->active = false;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function check(): void
    {
        $this->lastAction = "check";
    }

    public function bet(int $amount): void
    {
        $amount = min($amount, $this->stack + $this->currentBet);

        $this->stack -= $amount - $this->currentBet;
        $this->currentBet = $amount;
        $this->lastAction = "bet";

        if ($this->stack <= 0) {
            ///
            $this->allIn = true;
        }
    }

    public function call(int $amount): void
    {
        $amount = min($amount, ($this->stack + $this->currentBet));
        $this->stack -= $amount - $this->currentBet;
        $this->lastAction = "call";
        $this->currentBet = $amount;
    }

    public function callTest(int $amount): void
    {
        $amount = min($amount, ($this->stack + $this->currentBet));
        $this->stack -= $amount - $this->currentBet;
        $this->lastAction = "call";
        $this->currentBet = $amount;
    }

    public function fold(): void
    {
        // $this->currentBet = 0;
        $this->active = false;
        $this->currentStrength = "";
        $this->lastAction = "fold";
    }

    public function raise(int $bet): void
    {
        $minRaise = $bet * 2;
        $allChipsPlayerHas = $this->stack + $this->currentBet;
        $raise = min($minRaise, $allChipsPlayerHas);

        $this->stack -= $raise;
        $this->currentBet = $raise;
        $this->lastAction = "raise";

        if ($this->stack <= 0) {
            ///
            $this->allIn = true;
        }
    }

    public function getLastAction(): string
    {
        return $this->lastAction;
    }

    public function takePot(int $chips): void
    {
        $this->stack += $chips;
    }

    public function resetHand(): void
    {
        $this->hand = null;
    }

    public function chooseBetSize($potSize): int
    {
        return 0.75 * $potSize;
    }
}