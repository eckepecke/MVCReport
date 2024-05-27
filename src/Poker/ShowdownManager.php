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

    public function findWinner(array $players, array $board): object
    {
        $winners = $this->compare($players);

        if(count($winners) > 1) {
            $strength = $this->getWinningStrength($winners);
            $winner = $this->compareSameHands($winners, $strength, $board);
            echo"multiplewinner";
            $this->setShowdownWinner($winner);

            var_dump($winner->getName());
            return $winner;

        }
        $this->setShowdownWinner($winners[0]);
        echo"singleWinner";

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

    public function compareSameHands(array $players, string $strength, array $board): object
    {

        $playerHands = [];
        foreach ($players as $player) {
            $handObj = $player->getHand();
            $fullHand = array_merge($handObj->getCardArray(), $board);
            $playerHands[] = $fullHand;
        }
        switch ($strength) {
            case "High Card":
                $winnerIndex = $this->evaluator->compareHighCard($playerHands);
                $winner = $players[$winnerIndex];
            default:
                $winnerIndex = $this->evaluator->compareHighCard($playerHands);
                $winner = $players[$winnerIndex];
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
