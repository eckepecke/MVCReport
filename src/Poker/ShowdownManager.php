<?php

namespace App\Poker;

use Exception;
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

        $playerHandRanks = [];
        $playerHandSuits = [];


        foreach ($playerHands as $hand) {
            list($ranks, $suits) = $this->extractRanksAndSuits($hand);
            $playerHandRanks[] = $ranks;
            $playerHandSuits[] = $suits;
        }
        echo"lets see";
        var_dump($playerHandRanks);

        switch ($strength) {
            case "High card":
                $winnerIndex = $this->evaluator->compareHighCard($playerHandRanks);
                $winner = $players[$winnerIndex];
                break;
            case "One pair":
                $winnerIndex = $this->evaluator->compareOnePair($playerHandRanks);
                $winner = $players[$winnerIndex];
                break;
            case "Two pair":
                $winnerIndex = $this->evaluator->compareTwoPair($playerHandRanks);
                $winner = $players[$winnerIndex];
                break;
            case "Trips":
                $winnerIndex = $this->evaluator->compareTrips($playerHandRanks);
                $winner = $players[$winnerIndex];
                break;
            case "Straight":
                $winnerIndex = $this->evaluator->compareTrips($playerHandRanks);
                $winner = $players[$winnerIndex];
                break;
            case "Flush":
                $winnerIndex = $this->evaluator->compareFlushes($playerHandRanks, $playerHandSuits);
                $winner = $players[$winnerIndex];
                break;
            case "Full house":
                $winnerIndex = $this->evaluator->compareFullHouses($playerHandRanks);
                $winner = $players[$winnerIndex];
                break;
            case "Four of a kind":
                $winnerIndex = $this->evaluator->compareQuads($playerHandRanks);
                $winner = $players[$winnerIndex];
                break;
            default:
                throw new Exception("No such Handstrength $strength");
                break;
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
