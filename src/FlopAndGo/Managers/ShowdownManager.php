<?php

namespace App\FlopAndGo\Managers;

use App\FlopAndGo\HandChecker;

/**
 * A trait managing showdown.
 */
trait ShowdownManager
{
    public function showdown(): void
    {
        $this->assignHandStrengths();
        $winner = $this->gameProperties['handChecker']->compareStrength($this->gameProperties['hero'], $this->gameProperties['villain']);
        $winner->takePot($this->gameProperties['table']->getPotSize());

        $this->gameProperties['challenge']->setHandWinner($winner->getName());
        $this->gameProperties['table']->setStreet(4);

        $this->showdown = true;
        $this->gameProperties['challenge']->incrementHandsPlayed();
    }

    public function assignHandStrengths(): void
    {
        $board = $this->gameProperties['table']->getBoard();
        $fullHeroHand = array_merge($this->gameProperties['hero']->getHand(), $board);

        $heroStrength = $this->gameProperties['handChecker']->evaluateHand($fullHeroHand);
        $this->gameProperties['hero']->updateStrength($heroStrength);
        $this->gameProperties['handChecker']->resetStrengthArray();
        $fullVillainHand = array_merge($this->gameProperties['villain']->getHand(), $board);
        $villainStrength = $this->gameProperties['handChecker']->evaluateHand($fullVillainHand);
        $this->gameProperties['villain']->updateStrength($villainStrength);
    }

}
