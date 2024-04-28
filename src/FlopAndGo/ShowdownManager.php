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
        $this->table->incrementStreet();
        var_dump($crash);
    }

    public function assignHandStrengths()
    {
        $board = $this->table->getBoard();
        $fullHeroHand = array_merge($this->hero->getHoleCards(), $board);

        $heroStrength = $this->handChecker->evaluateHand($fullHeroHand);
        $this->hero->updateStrength($heroStrength);

        $this->handChecker->resetStrengthArray();

        $fullVillainHand = array_merge($this->villain->getHoleCards(), $board);
        $villainStrength = $this->handChecker->evaluateHand($fullVillainHand);
        $this->villain->updateStrength($villainStrength);
    }

}