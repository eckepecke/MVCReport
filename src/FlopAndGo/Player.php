<?php

namespace App\FlopAndGo;

use App\Cards\CardGraphic;

class Player
{
    protected int $stack;
    protected array $hand;
    protected string $position;
    protected int $currentBet;
    protected string $lastAction;

    public function __construct()
    {
        $this->stack = 5000;
        $this->hand = [];
        $this->currentBet = 0;
        $this->lastAction = "";
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
        // $this->lastAction = "call";
        $amount = min($amount, $this->stack);
        if ($amount === $this->stack) {
            $this->currentBet += $amount;
            $this->stack -= $amount;
            return;
        } 
        // if ($this->currentBet > 0 && $amount > $this->stack) {
        //     $this->stack -= ($amount - $this->currentBet);
        //     $this->currentBet += $amount;
        //     return;
        // }
        $this->stack -= $amount - $this->currentBet;

        $this->currentBet = $amount;
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

    public function getHand(): array
    {

        return $this->hand;
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

    public function getStack(): int
    {

        return $this->stack;
    }

    public function takePot(int $chips): void
    {
        $this->stack += $chips;
    }

    public function resetCurrentBet(): void
    {
        $this->currentBet = 0;
    }

    public function getCurrentBet(): int
    {
        return $this->currentBet;
    }

    public function setCurrentBet(int $amount): void
    {
        $this->currentBet = $amount;
    }

    public function getLastAction()
    {
        return $this->lastAction;
    }

    public function payAnte(int $ante) : void
    {
        $this->stack -= $ante;
    }
}
