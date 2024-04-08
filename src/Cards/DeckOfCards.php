<?php
namespace App\Cards;

use App\Cards\CardGraphic;

class DeckOfCards
{
    private $cards = [];

    public function __construct()
    {
        $cards = array(
            'diamonds' => array(
                'ace', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'jack', 'queen', 'king'
            ),
            'hearts' => array(
                'ace', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'jack', 'queen', 'king'
            ),
            'clubs' => array(
                'ace', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'jack', 'queen', 'king'
            ),
            'spades' => array(
                'ace', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'jack', 'queen', 'king'
            ),
        );

        $suits = ['diamonds', 'hearts', 'clubs', 'spades'];

        // Iterate over each suit
        foreach ($suits as $suit) {
            // Iterate over each value
            foreach (['ace', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'jack', 'queen', 'king'] as $value) {
                // Create a new CardGraphic object
                $cardGraphic = new CardGraphic();

                // Set the suit and value of the card graphic
                $cardGraphic->setSuit($suit);
                $cardGraphic->setValue($value);

                // Add the card graphic to the cards array
                $this->cards[] = $cardGraphic;
            }
        }
    }

    public function getCards() : array
    {
        return $this->cards;
    }

    public function getImgNames(): array
    {
        $imgNames = [];
        foreach ($this->cards as $card) {
            $imgNames[] = $card->getImgName();
        }
        return $imgNames;
    }

    public function shuffleDeck(): void
    {
        shuffle($this->cards);
    }
}