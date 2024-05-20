<?php

namespace App\Poker;
use App\Cards\CardHand;
use App\Poker\Dealer;


class CardManager extends Dealer
{
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

    public function dealCommunityCards(string $street, int $cardsDealt): array
    {

        switch ($street) {
            case "flop":
                if ($cardsDealt < 1) {
                    $flop = $this->dealFlop();
                    return $flop;
                }
                break;
            case "turn":
                if ($this->cardsDealt() < 4) {
                    $turn = $this->gameProperties['dealer']->dealOne();
                    $this->gameProperties['table']->registerOne($turn);
                }
                break;
            case "river":
                if ($this->cardsDealt() < 5) {
                    $river = $this->gameProperties['dealer']->dealOne();
                    $this->gameProperties['table']->registerOne($river);
                }
                break;
        }
    }
}