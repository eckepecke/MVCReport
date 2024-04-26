<?php

namespace App\Poker;

use App\Cards\CardGraphic;

class Player
{
    protected int $stack;
    protected array $hand;
    protected string $position;
    protected array $holeCards;
    protected int $currentBet;
    protected string $lastAction;



    public function __construct()
    {
        $this->stack = 5000;
        $this->hand = [];
        $this->currentBet = 0;
        $this->lastAction = "";

    }

    public function preflopCall(int $price): void
    {
        $this->stack -= $price;
        $this->currentBet += $price;
        $this->lastAction = "call";
    }

    public function fold(): void
    {
        $this->hand = [];
        $this->currentBet = 0;
        $this->lastAction = "fold";
    }


    public function bet(int $amount): void
    {
        $amount = min($amount, $this->stack + $this->currentBet);
        $this->stack -= $amount - $this->currentBet;
        $this->currentBet = $amount;
        $this->lastAction = "bet";

    }

    public function call(int $amount): void
    {
        $amount = min($amount, $this->stack);
        $this->stack -= $amount;
        $this->lastAction = "call";
    }

    public function receiveCard(CardGraphic $card): void
    {
        $this->hand[] = $card;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    public function getHoleCards(): array
    {

        return $this->hand;
    }

    public function muckCards(): void
    {
        $this->hand = [];
        $this->currentBet = 0;
    }

    public function getImgPaths(): array
    {
        $imgPaths = [];
        foreach ($this->hand as $card) {
            $imgPath = $card->getImgName();
            $imgPaths[] = $imgPath;
        }

        return $imgPaths;
    }

    public function payBlind(int $blind): void
    {
        $this->stack -= $blind;
    }

    public function getStack(): int
    {

        return $this->stack;
    }

    public function takePot(int $chips): void
    {
        $this->stack += $chips;
    }

    public function setCurrentBet(int $amount): void
    {
        $this->currentBet += $amount;
    }

    public function resetCurrentBet(): void
    {
        $this->currentBet = 0;
    }

    public function getCurrentBet(): int
    {
        return $this->currentBet;
    }

    public function getLastAction()
    {
        return $this->lastAction;
    }
}
