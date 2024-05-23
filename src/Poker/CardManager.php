<?php

namespace App\Poker;
use App\Poker\CardHand;
use App\Poker\Dealer;
use App\Poker\HandEvaluatorTrait;



class CardManager extends Dealer
{
    private object $evaluator;

    public function dealStartHandToAllPlayers(array $players): void
    {
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

    public function dealCommunityCards(string $street, int $cardsDealt): array
    {
        var_dump($street);
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

    public function assignStrength(array $players, array $board): void 
    {
        foreach ($players as $player) {
            $hand = $player->getHand();
            $cardsInHand = $hand->getCardArray();
            $fullHand = $this->fuseHandAndBoard($cardsInHand, $board);
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
}