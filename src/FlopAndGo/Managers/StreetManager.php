<?php

namespace App\FlopAndGo\Managers;

/**
 * A trait managing card and chip distribution.
 */
trait StreetManager
{
    public function handSetUp(): void
    {
        $this->gameProperties['hero']->fold();
        $this->gameProperties['villain']->fold();
        $this->gameProperties['table']->cleanTable();
        $this->gameProperties['table']->moveButton();
        $this->gameProperties['table']->getBombPotChips();
        $this->gameProperties['hero']->resetCurrentBet();
        $this->gameProperties['villain']->resetCurrentBet();
        $this->gameProperties['hero']->resetLastAction();
        $this->gameProperties['villain']->resetLastAction();
        $this->gameProperties['dealer']->shuffleCards();
        $this->gameProperties['dealer']->dealHoleCards();
        $this->gameProperties['table']->setStreet(1);
        $this->gameProperties['hero']->isAllIn();
        $this->gameProperties['villain']->isAllIn();
        $this->gameProperties['hero']->isAllIn();

        $this->showdown = false;
        $this->newHand = false;
        //$this->gameOver = false;
    }

    public function streetCheck(): int
    {
        return $this->gameProperties['table']->getStreet();
    }

    public function incrementStreet(): void
    {
        $current = $this->streetCheck();
        $this->gameProperties['table']->setStreet($current + 1);
    }

    public function cardsDealt(): int
    {
        $cardsDealt = count($this->gameProperties['table']->getBoard());
        return $cardsDealt;
    }

    public function dealCorrectStreet(): void
    {
        $street = $this->streetCheck();
        switch ($street) {
            case 1:
                if ($this->cardsDealt() < 1) {
                    $flop = $this->gameProperties['dealer']->dealFlop();
                    $this->gameProperties['table']->registerMany($flop);
                }
                break;
            case 2:
                if ($this->cardsDealt() < 4) {
                    $turn = $this->gameProperties['dealer']->dealOne();
                    $this->gameProperties['table']->registerOne($turn);
                }
                break;
            case 3:
                if ($this->cardsDealt() < 5) {
                    $river = $this->gameProperties['dealer']->dealOne();
                    $this->gameProperties['table']->registerOne($river);
                }
                break;
        }
    }

    public function allInCheck(object $player): void
    {
        if($player->isAllin()) {
            $this->gameProperties['dealer']->dealToShowdown();
            //$this->table->setStreet(4);
            $this->showdown();
        }


    }

    public function isNewHand(): bool
    {
        return $this->newHand;
    }
}
