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



    public function resetPlayerHands(array $players): void
    {
        foreach ($players as $player) {
            $player->resetHand();
        }
    }

    public function dealCommunityCards(string $street, int $cardsDealt): array
    {
        var_dump($street);

        switch ($street) {
            case "flop":
                if ($cardsDealt < 1) {
                    $flop = $this->dealFlop();
                    return $flop;
                }
                break;
            case "turn":
                if ($cardsDealt < 4) {
                    $turn = $this->dealOne();
                    return $turn;
                }
                break;
            case "river":
                if ($cardsDealt < 5) {
                    $river->dealOne();
                    return $river;
                }
                break;
        }
    }
}