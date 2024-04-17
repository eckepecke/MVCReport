<?php

namespace App\Poker;

use App\Cards\CardGraphic;

class Player
{
    protected $stack;
    protected $hand;
    protected $position;
    protected $holeCards;
    protected $cardHand;
    protected $currentBet;


    public function __construct() {
        $this->stack = 5000;
        $this->hand = [];
    }

    public function bet(int $amount): int {
        if ($amount > $this->stack) {
            $amount = $this->stack;
        }
        $this->stack -= $amount;
        return $amount;
    }

    public function call(int $amount): void {
        if ($amount > $this->stack) {
            $this->stack = 0;
        } else {
            $this->stack -= $amount;
        }
    }

    public function raise(int $raiseSize): void {
        $this->stack -= $raiseSize;
    }

    // public function receiveHoleCards(array $holeCards) : void {
    //     var_dump($holeCards);

    //     $this->hand = $holeCards;
    //     var_dump($this->holeCards);

    // }

    public function receiveCard(CardGraphic $card): void
    {
        $this->hand[] = $card;
    }

    public function getPosition() : string {
        return $this->position;
    }

    public function setPosition(string $position) : void {
        $this->position = $position;
    }

    public function getHoleCards() : array {
        
        return $this->hand;
    }

    public function muckCards() : void {
        $this->hand = [];
    }

    public function getImgPaths() : array {
        $imgPaths = [];
        foreach ($this->hand as $card) {
            $imgPath = $card->getImgName();
            $imgPaths[] = $imgPath;
        }
        
        return $imgPaths;
    }

    public function payBlind(int $blind) : void {
        $this->stack -= $blind;
    }

    public function getStack() : int {
        
        return $this->stack;
    }

    public function takePot(int $chips) : void {
        $this->stack += $chips;
    }

    public function setCurrentBet(int $amount) : void {
        $this->currentBet += $amount;
    }

    public function resetCurrentBet() : void {
        $this->currentBet = 0;
    }

    public function getCurrentBet() : int {
        return $this->currentBet;
    }
}