<?php

namespace App\Cards;

class Card
{
    protected mixed $value;
    protected string $suit;
    protected array $card;


    public function __construct()
    {
        $cards = array(
            'suits' => array(
                'hearts', 'diamonds', 'clubs', 'spades'
            ),
            'values' => array(
                'ace', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'jack', 'queen', 'king'
            )
        );

        $suit = $cards['suits'][rand(0, 3)];
        $value = $cards['values'][rand(0, 12)];


        $this->value = $value;
        $this->suit = $suit;
        $this->card = [$suit, $value];

    }

    public function getCardString(): string
    {
        return $this->value . ' of ' . $this->suit;
    }

    public function getValue(): string
    {
        return (string) $this->value;
    }

    public function getsuit(): string
    {
        return $this->suit;
    }
}
