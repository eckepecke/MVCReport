<?php

namespace App\Poker;

class Table
{
    protected int $potSize;
    protected array $flop;
    protected array $fullBoard;
    protected string $street;

    public function __construct()
    {
        $this->potSize = 0;
        $this->flop = [];
        $this->fullBoard = [];
        $this->street = "";
    }

    public function addChipsToPot(int $chips): void
    {
        $this->potSize += $bet;
    }

    public function getPotSize(): int
    {
        return $this->potSize;
    }

    public function registerMany(array $cards): void
    {
        $this->flop = $cards;
        foreach ($cards as $card) {
            $this->fullBoard[] = $card;
        }
    }

    public function registerOne(object $card): void
    {
        $this->fullBoard[] = $card;
    }

    public function getFlop(): array
    {
        return $this->flop;
    }

    public function setStreet(): void {
        if (count($this->fullBoard) === 0) {
            $this->street = "";
        }

        if (count($this->fullBoard) === 3) {
            $this->street = "flop";
        }

        if (count($this->fullBoard) === 4) {
            $this->street = "turn";
        }

        if (count($this->fullBoard) === 5) {
            $this->street = "river";
        }
    }

    public function getBoard(): array
    {
        return $this->fullBoard;
    }

    public function getStreet(): int
    {
        return $this->street;
    }

    public function cleanTable()
    {
        $this->potSize = 0;
        $this->flop = [];
        $this->fullBoard = [];
        $this->street = "";
    }
}
