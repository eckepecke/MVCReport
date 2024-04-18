<?php

namespace App\Poker;

class Table
{
    protected $potSize;
    protected $flop;
    protected $turn;
    protected $river;
    protected $fullBoard;
    protected $street;


    public function __construct() {
        $this->potSize = 0;
        $this->flop = [];
        $this->turn = "";
        $this->river = "";
        $this->fullBoard = [];
        $this->street = 1;

    }

    public function addChipsToPot(array $bets) : void
    {
        foreach ($bets as $bet) {
            $this->potSize += $bet;
        }
    }

    public function getPotSize() : int
    {
        return $this->potSize;
    }

    public function resetPotSize() : void
    {
        $this->potSize = 0;
    }

    public function registerFlop(array $cards) : void
    {
        $this->flop = $cards;
        foreach ($cards as $card) {
            $this->fullBoard[] = $card;
        }
    }

    public function registerTurn(string $turn) : void
    {
        $this->turn = $turn;
        $this->fullBoard[] = $turn;
    }

    public function registerRiver(string $river) : void
    {
        $this->river = $river;
        $this->fullBoard[] = $river;
    }

    public function getStreet() : int
    {
        return $this->street;
    }

    public function setStreet(int $street) : void
    {
        $this->street = $street;
    }
}