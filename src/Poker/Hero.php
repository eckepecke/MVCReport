<?php

namespace App\Poker;

use App\Poker\CardHand;
/**
 * Class Hero
 *
 * Represents the hero player in the poker game.
 */
class Hero extends Player
{
    /** @var bool Indicates if the player is the hero. */
    protected bool $isHero = true;

    /**
     * Constructor for the Hero class.
     */
    public function __construct()
    {
        parent::__construct();
        $this->isHero = false;
        $this->stack = 200;
    }

    /**
     * Checks if the player is the hero.
     *
     * @return bool True if the player is the hero, false otherwise.
     */
    public function isHero(): bool
    {
        return $this->isHero;
    }
}
