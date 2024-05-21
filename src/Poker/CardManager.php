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
        var_dump(count($cards));
        return $cards;
    }
}