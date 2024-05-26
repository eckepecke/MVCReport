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
    private ?object $evaluator = null;

    public function add(SameHandEvaluator $evaluator): void
    {
        $this->evaluator = $evaluator;
    }

    public function findWinner(array $players): object
    {
        $winners = $this->compare($players);


        $multipleWinners = false;
        if(count($winners) > 1) {
            $multipleWinners = true;
            $strength = $this->getWinningStrength($winners);
            $winner = $this->compareSameHands($winners, $strength);
            return $winner;

        }
        $this->setShowdownWinner($winners[0]);
        return $winners[0];
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

    public function compareSameHands(array $players, $strength): object
    {

        $playerHands = [];
        foreach ($players as $player) {
            $playerHands[] = $player->getHand();
        }
        switch ($strength) {
            case "High Card":
                $winnerIndex = $this->evaluator->compareHighCard($playerHands);
                $winner = $players[$winnerIndex];
            default:
            //debug
                $winnerIndex = $this->evaluator->compareHighCard($playerHands);
                $winner = $players[$winnerIndex];
                var_dump($winnerIndex);
                var_dump($winner->getName());

                var_dump($crash);
        }
        return $winner;
    }


    public function getWinningStrength(array $players): string
    {
        $temp = $players[0]->getHand();
        $winningStrength = $temp->getStrengthString();
        return $winningStrength;
    }
}
