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
    }

    public function streetCheck() : int 
    {
        return $this->table->getStreet();
    }

    public function incrementStreet() : string 
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

    public function dealCorrectStreet($street) : void 
    {
        echo "Här är jag";
        switch ($street) {
            case 1:
                if ($this->cardsDealt() < 1) {
                    $flop = $this->dealer->dealFlop();
                    $this->table->registerMany($flop);
                }
                break;
            case 2:
                if ($this->cardsDealt() < 4) {
                    $turn = $this->dealer->dealOne();
                    $this->table->registerMany($turn);
                }
                break;
            case 3:
                if ($this->cardsDealt() < 5) {
                    $river = $this->dealer->dealOne();
                    $this->table->registerMany($river);
                }
                break;
        }

    }
}
