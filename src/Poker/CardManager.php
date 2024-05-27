<?php

namespace App\Poker;

use App\Poker\CardHand;
use App\Poker\Dealer;
// use App\Poker\HandEvaluatorTrait;

class CardManager extends Dealer
{
    private object $evaluator;

    public function dealStartingHands(array $players): void
    {
        $this->shuffleCards();
        foreach ($players as $player) {
            $cards = $this->dealStartHand();
            $hand = new CardHand();
            $hand->add($cards[0]);
            $hand->add($cards[1]);
            $player->addHand($hand);
        }
    }

    public function resetPlayerHands(array $players): void
    {
        foreach ($players as $player) {
            $player->resetHand();
        }
    }

    public function activatePlayers(array $players): void
    {
        foreach ($players as $player) {
            $player->activate();
        }
    }

    public function dealCommunityCards(string $street, int $cardsDealt): array
    {
        $cards = [];

        switch ($street) {
            case "flop":
                if ($cardsDealt < 1) {
                    $cards = $this->dealFlop();
                }
                break;
            case "turn":
                if ($cardsDealt < 4) {
                    $cards = $this->dealOne();
                }
                break;
            case "river":
                if ($cardsDealt < 5) {
                    $cards = $this->dealOne();
                }
                break;
        }
        return $cards;
    }

    public function addEvaluator(HandEvaluator $evaluator): void
    {
        $this->evaluator = $evaluator;
    }

    public function updateHandStrengths(array $players, array $board): void
    {
        foreach ($players as $player) {
            $hand = $player->getHand();
            $cardsInHand = $hand->getCardArray();
            $fullHand = $this->fuseHandAndBoard($cardsInHand, $board);
            // $this->addBoardToHand($player, $fullHand);

            $strengthArray = $this->evaluator->evaluateHand($fullHand);
            $strength = $this->evaluator->getCurrentStrength($strengthArray);
            $hand->setStrengthString($strength);
            $strengthAsInt = $this->evaluator->getStrengthAsInt($strength);
            $hand->setStrengthInt($strengthAsInt);
        }
    }

    public function fuseHandAndBoard(array $holeCards, array $board): array
    {
        return array_merge($holeCards, $board);
    }

    public function dealRemaining(array $board): array
    {
        $remaining = 5 - count($board);
        $cards = $this->deck->drawMany($remaining);
        return $cards;
    }
}
