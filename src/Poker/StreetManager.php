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
    private string $street = "preflop";
    private bool $showdown = false;
    private bool $preflop = true;



    /**
     * Array containing all possible streets.
     *
     * @var array
     */
    private array $streetArray = [
        "preflop",
        "flop",
        "turn",
        "river",
        "showdown"
    ];

    /**
     * Sets the next street in the street array.
     * If the current street is the last one, it resets to the first street.
     *
     * @return void
     */
    public function setNextStreet(): void
    {
        $currentIndex = array_search($this->street, $this->streetArray);
        $nextIndex = $currentIndex + 1;

        if ($nextIndex === 4) {

            $this->showdown = true;
            $nextIndex = 0;
        }
        $this->street = $this->streetArray[$nextIndex];
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getShowdown(): bool
    {
        return $this->showdown;
    }

    public function setShowdownTrue(): void
    {
        $this->showdown = true;
    }

    public function setShowdownFalse(): void
    {
        $this->showdown = false;
    }

    public function resetStreet(): void
    {
        $this->street = "preflop";
    }

    public function isPreflop(): bool
    {
        return $this->preflop;
    }

    public function isPostflop(): void
    {
        echo"changing prop to false";
        $this->preflop = false;
    }
}
