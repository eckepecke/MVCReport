<?php

namespace App\Poker;

/**
 * A trait that takes an array and returns the first Texas true item.
 */
trait TexasHandTrait
{

    private array $strengthArray  = [
        'Royal flush' => false,
        'Straight flush' => false,
        'Four of a kind' => false,
        'Full house' => false,
        'Flush' => false,
        'Straight' => false,
        'Three of a kind' => false,
        'Two pair' => false,
        'One pair' => false,
        'High card' => true
    ];

    public function resetStrengthArray(): void
    {
        $this->strengthArray = [
            'Royal flush' => false,
            'Straight flush' => false,
            'Four of a kind' => false,
            'Full house' => false,
            'Flush' => false,
            'Straight' => false,
            'Three of a kind' => false,
            'Two pair' => false,
            'One pair' => false,
            'High card' => true
        ];
    }

    public function getCurrentStrength(): string
    {
        foreach ($this->strengthArray as $key => $value) {
            if ($value === true) {
                return $key;
            }
        }

        return 'No strength found';
    }


}