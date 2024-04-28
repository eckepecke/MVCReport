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
        $winner->takePot($this->table->getPotsize());
        $this->challenge->setHandWinner($winner->getName());
       // $this->incrementStreet();

        $this->showdown = true;
        //$this->newHand = true;
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