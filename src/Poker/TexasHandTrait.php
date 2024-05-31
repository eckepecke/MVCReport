<?php

namespace App\Poker;

/**
 * A trait that takes an array and returns the first Texas true item.
 */
trait TexasHandTrait
{
    /**
     * @var array An array representing the strength of various poker hands.
     */
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

    /**
     * Resets the strength array to its initial state.
     *
     * Sets all hand strengths to false except for 'High card'.
     *
     * @return void
     */
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

    /**
     * Gets the current hand strength.
     *
     * Iterates through the strength array and returns the first hand strength that is true.
     *
     * @return string The current hand strength or 'No strength found' if none are true.
     */
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
