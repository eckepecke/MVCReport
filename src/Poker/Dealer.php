<?php

namespace App\Poker;

use App\Cards\DeckOfCards;

/**
 * Class Dealer
 *
 * Manages dealing cards and setting the price to play.
 */
class Dealer
{
    /** @var DeckOfCards The deck of cards used for dealing. */
    protected object $deck;

    /** @var int The price to play set by the dealer. */
    protected int $priceToPlay;

    /**
     * Adds a deck of cards to the dealer.
     *
     * @param DeckOfCards $deck The deck of cards to be added.
     * @return void
     */
    public function addDeck(DeckOfCards $deck): void
    {
        $this->deck = $deck;
    }

    /**
     * Deals the flop - three community cards.
     *
     * @return CardGraphic[] An array containing the flop cards.
     */
    public function dealFlop(): array
    {
        $flop = $this->deck->drawMany(3);
        return $flop;
    }

    /**
     * Deals one community card.
     *
     * @return CardGraphic[] An array containing the dealt community card.
     */
    public function dealOne(): array
    {
        $card = $this->deck->drawOne();
        return [$card];
    }

    /**
     * Shuffles the deck of cards.
     *
     * This method initializes the deck of cards and then shuffles it.
     *
     * @return void
     */
    public function shuffleCards(): void
    {
        $this->deck->initializeCards();
        $this->deck->shuffleDeck();
    }

    /**
     * Deals the starting hand for a player.
     *
     * @return CardGraphic[] An array containing the starting hand cards.
     */
    public function dealStartHand(): array
    {
        $cards = $this->deck->drawMany(2);
        return $cards;
    }
}
