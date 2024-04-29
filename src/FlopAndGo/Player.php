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
    protected bool $allIn;


    public function __construct()
    {
        $this->stack = 5000;
        $this->hand = [];
        $this->currentBet = 0;
        $this->lastAction = "";
        $this->allIn = false;

    }

    public function bet(int $amount): void
    {
        echo "player bet";
        var_dump($amount);

        $amount = min($amount, $this->stack + $this->currentBet);
        var_dump($amount);

        $this->stack -= $amount - $this->currentBet;
        $this->currentBet = $amount;
        $this->lastAction = "bet";

        if ($this->stack <= 0){
            ///
            $this->allIn = true;
        }
    }

    public function call(int $amount): void
    {
        echo "call triggered";

        var_dump($this->stack);
        $amount = min($amount, ($this->stack + $this->currentBet));
        $this->stack -= $amount - $this->currentBet;
        $this->lastAction = "call";
        $this->currentBet = $amount;

        var_dump($amount);
        var_dump($this->stack);


        // echo "call triggered";
        // $this->lastAction = "call";

        // $amount = min($amount, $this->stack);
        // var_dump($amount);

        // if ($amount === $this->stack) {
        //     $this->currentBet += $amount;
        //     $this->stack -= $amount;
        //     return $amount;
        // } 

        // $this->stack -= $amount - $this->currentBet;
        // $this->currentBet = $amount;
        // return $amount;

    }

    public function check(): void
    {
        $this->lastAction = "check";
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

    public function isAllIn () : bool 
    {
        echo"isAllin";
        $this->allIn = false;
        if ($this->stack <= 0) {
            $this->allIn = true;
        }
        return $this->allIn;
    }
}
