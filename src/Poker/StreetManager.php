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
    private bool $showdown = false;


    /**
     * Array containing all possible streets.
     *
     * @var array
     */
    private array $streetArray = [
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
        echo "setting next";
        $currentIndex = array_search($this->street, $this->streetArray);
        $nextIndex = $currentIndex + 1;
        var_dump($nextIndex);

        if ($nextIndex === 3) {
            echo "setting sd true";

            $this->showdown = true;
            $nextIndex = 0;
        }
        $this->street = $this->streetArray[$nextIndex];
        var_dump($this->street);
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getShowdown(): bool
    {
        echo"hej";
        var_dump($this->showdown);
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


}