<?php

namespace App\Poker;

use App\Cards\CardGraphic;
use App\Poker\TexasHandTrait;

/**
 * Class CardHand
 *
 * Represents a hand of cards.
 */
class CardHand
{
    use TexasHandTrait;

    /** @var array The array to store the cards in the hand. */
    private array $hand = [];

    /** @var string Represents the hand's strength in string format. */
    private string $strengthString = "";

    /** @var int Represents the hand's strength in integer format. */
    private int $strengthInt = 0;

    /**
     * Adds a CardGraphic object to the hand.
     *
     * @param CardGraphic $card The card to add.
     * @return void
     */
    public function add(CardGraphic $card): void
    {
        $this->hand[] = $card;
    }

    /**
     * Gets the number of cards in the hand.
     *
     * @return int The number of cards in the hand.
     */
    public function getNumberCards(): int
    {
        return count($this->hand);
    }

    /**
     * Retrieves the values of cards in the hand.
     *
     * @return array An array containing the string representations of the card values.
     */
    public function getCardValues(): array
    {
        $values = [];
        foreach ($this->hand as $card) {
            $values[] = $card->getCardString();
        }
        return $values;
    }

    /**
     * Retrieves an array of card values.
     *
     * @return array An array containing the string representations of the card values.
     */
    public function getValueArray(): array
    {
        $values = [];
        foreach ($this->hand as $card) {
            $values[] = $card->getCardString();
        }
        return $values;
    }

    /**
     * Retrieves an array of card image names.
     *
     * @return array An array containing the names of the card images.
     */
    public function getImgNames(): array
    {
        $values = [];
        foreach ($this->hand as $card) {
            $values[] = $card->getImgName();
        }
        return $values;
    }
    /**
     * Retrieves an array of cards in the hand.
     *
     * @return array An array containing the cards in the hand.
     */
    public function getCardArray(): array
    {
        return $this->hand;
    }

    /**
     * Retrieves the strength string of the hand.
     *
     * @return string The strength string of the hand.
     */
    public function getStrengthString(): string
    {
        return $this->strengthString;
    }

    /**
     * Sets the strength string of the hand.
     *
     * @param string $strength The strength string to set.
     * @return void
     */
    public function setStrengthString(string $strength): void
    {
        $this->strengthString = $strength;
    }

    /**
     * Retrieves the strength integer of the hand.
     *
     * @return int The strength integer of the hand.
     */
    public function getStrengthInt(): int
    {
        return $this->strengthInt;
    }

    /**
     * Sets the strength integer of the hand.
     *
     * @param int $strength The strength integer to set.
     * @return void
     */
    public function setStrengthInt(int $strength): void
    {
        $this->strengthInt = $strength;
    }
}
