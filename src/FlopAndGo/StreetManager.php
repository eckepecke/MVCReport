<?php

namespace App\FlopAndGo;

/**
 * A trait managing card and chip distribution.
 */
trait StreetManager
{
    public function handSetUp() : void
    {
        $this->table->moveButton();
        $this->table->getBombPotChips();
        $this->dealer->dealHoleCards();
        $this->newHand = false;
        $this->table->setStreet(1);
    }

    public function streetCheck() : int 
    {
        return $this->table->getStreet();
    }

    public function incrementStreet() : void 
    {
        $current = $this->streetCheck();
        $this->table->setStreet($current + 1);
    }

    public function cardsDealt() : int
    {
        echo"cardsdealt";
        $cardsDealt = count($this->table->getBoard());
        var_dump($cardsDealt);
        return $cardsDealt;
    }

    public function dealCorrectStreet() : void 
    {
        echo "dealcorrectStreet";
        $street = $this->streetCheck();
        var_dump($street);
        switch ($street) {
            case 1:
                if ($this->cardsDealt() < 1) {
                    $flop = $this->dealer->dealFlop();
                    $this->table->registerMany($flop);
                }
                break;
            case 2:
                echo "Hej 2";
                if ($this->cardsDealt() < 4) {
                    $turn = $this->dealer->dealOne();
                    $this->table->registerOne($turn);
                }
                break;
            case 3:
                echo "Hej 3";
                if ($this->cardsDealt() < 5) {
                    $river = $this->dealer->dealOne();
                    $this->table->registerOne($river);
                }
                break;
            // case 4:
            //     echo "Hej 4";
            //     $this->showdown();
            //     break;
        }
    }

    public function isShowdown() :bool 
    {
        echo "isShowdown";
        var_dump($this->streetCheck());
        return ($this->streetCheck() === 4);
    }
}
