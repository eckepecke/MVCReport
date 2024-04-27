<?php

namespace App\FlopAndGo;

class Table
{
    protected int $potSize;
    protected array $flop;
    protected array $fullBoard;
    protected string $street;
    private object $sbPlayer;
    private object $bbPlayer;

    public function __construct()
    {
        $this->potSize = 0;
        $this->flop = [];
        $this->fullBoard = [];
        $this->street = "";
    }

    public function seatPlayers(object $player1, object $player2): void
    {
        $this->sbPlayer = $player1;
        $this->sbPlayer->setPosition("SB");

        $this->bbPlayer = $player2;
        $this->bbPlayer->setPosition("BB");

    }

    public function addChipsToPot(int $chips): void
    {
        $this->potSize += $chips;
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
        $this->street = "";

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

    public function getStreet(): string
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

    public function moveButton(): void
    {
        $temp = $this->sbPlayer;
        $this->sbPlayer = $this->bbPlayer;
        $this->bbPlayer = $temp;

        $this->bbPlayer->setPosition("BB");
        $this->sbPlayer->setPosition("SB");
    }
}
