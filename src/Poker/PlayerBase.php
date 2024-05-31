<?php

namespace App\Poker;

use App\Poker\CardHand;

class PlayerBase
{
    protected int $stack;
    protected ?object $hand;
    protected int $currentBet;
    protected string $lastAction;
    protected bool $allIn;
    protected bool $active;


    public function __construct()
    {
        $this->stack = 5000;
        $this->hand = null;
        $this->currentBet = 0;
        $this->lastAction = "";
        $this->allIn = false;
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
        $this->stack -= $amount;
        $this->lastAction = "call";
        $this->currentBet = $amount + $this->currentBet;
    }

    public function fold(): void
    {
        // $this->currentBet = 0;
        $this->active = false;
        // $this->currentStrength = "";
        $this->lastAction = "fold";
    }

    public function raise(int $bet): void
    {
        $minRaise = $bet * 2;
        $allChipsPlayerHas = $this->stack + $this->currentBet;
        $raise = min($minRaise, $allChipsPlayerHas);

        $this->stack -= $raise - $this->currentBet;
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

    public function resetLastAction(): void
    {
        $this->lastAction = "";
    }

    public function resetHand(): void
    {
        $this->hand = null;
    }

    public function isAllin(): bool
    {
        if ($this->stack <= 0) {
            $this->allIn = true;
        }
        return $this->allIn;
    }

}
