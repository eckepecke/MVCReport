<?php

namespace App\FlopAndGo\Managers;

use App\FlopAndGo\Game;

use App\FlopAndGo\Managers\BetSizeManager;
use App\FlopAndGo\Managers\GameStatusManager;
use App\FlopAndGo\Managers\HeroActionManager;
use App\FlopAndGo\Managers\ShowdownManager;
use App\FlopAndGo\Managers\StreetManager;
use App\FlopAndGo\Managers\VillainActionManager;
use App\FlopAndGo\Managers\VillainFacingActionManager;

/**
 * Manages the game.
 */
class Manager
{
    use BetSizeManager;
    use GameStatusManager;
    use HeroActionManager;
    use ShowdownManager;
    use StreetManager;
    use VillainActionManager;
    use VillainFacingActionManager;

    private bool $newHand = true;
    private bool $showdown = false;
    private bool $gameOver = false;
    private object $game;
    private array $gameProperties;


    public function addGame(Game $game): void
    {
        $this->game = $game;
    }

    public function addGameProperties(): void
    {
        $this->gameProperties = $this->game->getAllProperties();
    }

    public function allHandsHavePlayed(): void
    {
    // Check if challenge is over
    if ($this->gameProperties['challenge']->challengeComplete()) {
        $this->gameOver = true;
    }
    }

    public function setUpStreet(mixed $action): void
    {
    // Check if a new hand is starting
    if ($this->newHand === true || $action === "next") {
        $this->handSetUp();
    }

    }

    public function updateShowdownProp(): void
    {
    if  ($this->streetCheck() === 4){
        $this->showdown = true;
    };
    }

    public function newHandCheck(): bool 
    {
        return $this->newHand;
    }

    public function isShowdown(): bool 
    {
        return $this->showdown;
    }

    public function gameOverCheck(): bool 
    {
        return $this->gameOver;
    }
}
