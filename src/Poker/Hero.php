<?php

namespace App\Poker;

use App\Poker\CardHand;
// use App\Entity\StatsTracker;

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
        $this->stack = 2000;
        // $this->statsTracker = null;
    }

    // public function getTracker(): ?StatsTracker
    // {
    //     return $this->statsTracker;
    // }

    // public function setStatsTracker(?StatsTracker $statsTracker): void
    // {
    //     $this->statsTracker = $statsTracker;
    // }

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
