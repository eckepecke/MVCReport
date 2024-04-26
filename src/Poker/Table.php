<?php

namespace App\Poker;

class Table
{
    protected int $potSize;
    protected array $flop;
    protected array $fullBoard;
    protected int $street;

    public function __construct()
    {
        $this->potSize = 0;
        $this->flop = [];
        $this->fullBoard = [];
        $this->street = 1;
    }

    public function addChipsToPot(int $bet): void
    {
        $this->potSize += $bet;
    }

    public function getPotSize(): int
    {
        return $this->potSize;
    }

    public function resetPotSize(): void
    {
        $this->potSize = 0;
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

    public function getTurn(): object
    {
        return $this->fullBoard[3] ?? [];
    }

    public function getRiver(): object
    {
        return $this->fullBoard[4] ?? [];
    }

    public function getBoard(): array
    {
        return $this->fullBoard;
    }

    public function getStreet(): int
    {
        return $this->street;
    }

    public function incrementStreet(): void
    {
        echo "incrementing";
        if ($this->street === 4) {
            $this->street = 1;
            return;
        }
        var_dump($this->street);
        $this->street += 1;
        var_dump($this->street);

    }

    public function cleanTable()
    {
        $this->potSize = 0;
        $this->flop = [];
        $this->fullBoard = [];
        $this->street = 1;
    }
}
