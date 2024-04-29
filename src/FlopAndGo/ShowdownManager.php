<?php

namespace App\FlopAndGo;

/**
 * A trait managing showdown.
 */
trait ShowdownManager
{
    public function showdown() {
        $this->assignHandStrengths();
        $winner = $this->handChecker->compareStrength($this->hero, $this->villain);
        echo "showdown";
        var_dump($winner->getStack());
        
        $winner->takePot($this->table->getPotsize());
        var_dump($winner->getStack());

        $this->challenge->setHandWinner($winner->getName());
        $this->table->setStreet(4);
        var_dump($this->table->getPotSize());



        $this->showdown = true;
        $this->challenge->incrementHandsPlayed();
        // $this->newHand = true;
    }

    public function assignHandStrengths()
    {
        $board = $this->table->getBoard();
        $fullHeroHand = array_merge($this->hero->getHand(), $board);

        $heroStrength = $this->handChecker->evaluateHand($fullHeroHand);
        $this->hero->updateStrength($heroStrength);

        $this->handChecker->resetStrengthArray();

        $fullVillainHand = array_merge($this->villain->getHand(), $board);
        $villainStrength = $this->handChecker->evaluateHand($fullVillainHand);
        $this->villain->updateStrength($villainStrength);
    }

}