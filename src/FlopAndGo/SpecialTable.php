<?php

namespace App\FlopAndGo;

class SpecialTable extends Table
{
    private object $sbPlayer;
    private object $bbPlayer;
    private int $ante;


    public function __construct()
    {

        parent::__construct();
        $this->ante = 200;
    }

    public function seatPlayers(object $player1, object $player2): void
    {
        $this->sbPlayer = $player1;
        $this->sbPlayer->setPosition("SB");

        $this->bbPlayer = $player2;
        $this->bbPlayer->setPosition("BB");
    }

    public function moveButton(): void
    {
        $temp = $this->sbPlayer;
        $this->sbPlayer = $this->bbPlayer;
        $this->bbPlayer = $temp;

        $this->bbPlayer->setPosition("BB");
        $this->sbPlayer->setPosition("SB");
    }

    public function getSbPlayer(): object
    {
        return $this->sbPlayer;
    }

    public function getBbPlayer(): object
    {
        return $this->bbPlayer;
    }

    public function getPriceToPlay(): int
    {
        $amountOne = $this->bbPlayer->getCurrentBet();
        $amountTwo = $this->sbPlayer->getCurrentBet();

        $biggestAmount = max($amountOne, $amountTwo);
        $smallestAmount = min($amountOne, $amountTwo);

        return $biggestAmount - $smallestAmount;
    }

    public function getMinimumRaiseAllowed(): int
    {
        $amountOne = $this->bbPlayer->getCurrentBet();
        $amountTwo = $this->sbPlayer->getCurrentBet();

        $biggestAmount = max($amountOne, $amountTwo);
        // If no bets have been placed, min bet = 50
        $biggestAmount = max($biggestAmount, 25);
        return 2 * $biggestAmount;
    }

    public function getCardImages(): array
    {
        $imgPaths = [];
        foreach ($this->fullBoard as $card) {
            $imgPath = $card->getImgName();
            $imgPaths[] = $imgPath;
        }

        return $imgPaths;
    }

    public function getBombPotChips(): void
    {
        $this->bbPlayer->payAnte($this->ante);
        $this->sbPlayer->payAnte($this->ante);
        $this->addChipsToPot(2 * $this->ante);
    }

    public function setStreet(int $street): void
    {
        if ($this->street === 4) {
            $this->street = 1;
            return;
        }
        $this->street = $street;
    }

}
