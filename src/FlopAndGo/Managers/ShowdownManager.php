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
        $handChecker = new HandChecker();
        $winner = $handChecker->compareStrength($this->hero, $this->villain);

        $winner->takePot($this->table->getPotSize());

        $this->challenge->setHandWinner($winner->getName());
        $this->table->setStreet(4);

        $this->showdown = true;
        $this->challenge->incrementHandsPlayed();
    }

    public function assignHandStrengths(): void
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
