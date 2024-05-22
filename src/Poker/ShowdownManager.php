<?php

namespace App\Poker;

/**
 * Class ShowDownManager
 * 
 * Manages betting logic in a poker game.
 */
class ShowDownManager
{
    public function chipsToWinner(): void
    {
        $this->assignHandStrengths();
        $winner = $this->gameProperties['handChecker']->compareStrength($this->gameProperties['hero'], $this->gameProperties['villain']);
        $winner->takePot($this->gameProperties['table']->getPotSize());

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