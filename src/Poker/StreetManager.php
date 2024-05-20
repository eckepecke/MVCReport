<?php

namespace App\Poker;

/**
 * Class StreetManager
 * 
 * Manages the positions of players in a poker game.
 */
class StreetManager
{
    /**
     * The current street.
     *
     * @var string
     */
    private string $street = "flop";

    /**
     * Array containing all possible streets.
     *
     * @var array
     */
    private array $streetArray = [
        "flop",
        "turn",
        "river"
    ];

    /**
     * Sets the next street in the street array.
     * If the current street is the last one, it resets to the first street.
     *
     * @return void
     */
    public function setNextStreet(): void
    {
        $index = array_search($this->street, $this->streetArray);

        if ($this->index === 2) {
            $this->index = 0;
            return;
        }
        $this->street = $streetArray[$index];
    }

    public function getStreet(): string
    {
        return $this->street;
    }


}