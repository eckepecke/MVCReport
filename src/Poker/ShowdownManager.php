<?php

namespace App\Poker;

/**
 * Class ShowDownManager
 *
 * Manages betting logic in a poker game.
 */
class ShowdownManager extends HandEvaluator
{
    private ?object $showdownWinner = null;

    public function findWinner(array $players): object
    {
        $winner = $this->compare($players);

        $multipleWinners = false;
        if(count($winner) > 1) {
            $multipleWinners = true;
            var_dump($needMoreComparison);
        }
        $this->setShowdownWinner($winner[0]);
        return $winner[0];
    }

    public function setShowdownWinner(object $winner): void
    {
        $this->showdownWinner = $winner;
    }
    public function nullShowdownWinner(): void
    {
        $this->showdownWinner = null;
    }

    public function getWinner(): ?object
    {
        return $this->showdownWinner;
    }

    public function compare(array $players): array
    {
        $toBeat = 0;
        $winners = [];

        foreach ($players as $player) {
            $hand = $player->getHand();
            $score = $hand->getStrengthInt();

            if ($score === $toBeat) {
                $winners[] = $player;
            }

            if ($score > $toBeat) {
                $winners = [];
                $winners[] = $player;
                $toBeat = $score;
            }

        }

        return $winners;
    }
}
