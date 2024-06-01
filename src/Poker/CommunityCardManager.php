<?php

namespace App\Poker;

/**
 * Class CommunityCardManager
 *
 * Manages the community cards on the board.
 */
class CommunityCardManager
{
    /** @var array An array to store the community cards on the board. */
    protected array $board;

    public function __construct()
    {
        $this->board = [];
    }

    /**
     * Registers new cards to the community board.
     *
     * @param CardGraphic[] $cards An array of CardGraphic objects to be added to the board.
     * @return void
     */
    public function register(array $cards): void
    {
        foreach ($cards as $card) {
            $this->board[] = $card;
        }
    }

    /**
     * Retrieves the current community cards on the board.
     *
     * @return CardGraphic[] An array containing the current community cards on the board.
     */
    public function getBoard(): array
    {
        return $this->board;
    }

    /**
     * Resets the community cards on the board.
     *
     * @return void
     */
    public function resetBoard(): void
    {
        $this->board = [];
    }

    /**
     * Gets the number of cards already dealt on the board.
     *
     * @return int The number of cards already dealt on the board.
     */
    public function cardsDealt(): int
    {
        $cardsDealt = count($this->board);
        return $cardsDealt;
    }
}
