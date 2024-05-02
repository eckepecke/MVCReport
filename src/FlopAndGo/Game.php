<?php

namespace App\FlopAndGo;

use App\FlopAndGo\BetSizeManager;
use App\FlopAndGo\Dealer;
use App\FlopAndGo\HandChecker;
use App\FlopAndGo\Hero;
use App\FlopAndGo\HeroActionManager;
use App\FlopAndGo\Moderator;
use App\FlopAndGo\ShowdownManager;
use App\FlopAndGo\SpecialTable;
use App\FlopAndGo\StreetManager;
use App\FlopAndGo\Table;
use App\FlopAndGo\Villain;
use App\FlopAndGo\VillainActionManager;


class Game
{
    use BetSizeManager;
    use GameStatusManager;
    use HeroActionManager;
    use ShowdownManager;
    use StreetManager;
    use VillainActionManager;

    public object $hero;
    private object $villain;
    private object $dealer;
    private object $table;
    private object $handChecker;
    private object $moderator;
    private object $challenge;
    private bool $newHand = true;
    private bool $showdown = false;
    private bool $gameOver = false;

    public function addHero(Hero $hero): void
    {
        $this->hero = $hero;
    }

    public function addVillain(Villain $villain): void
    {
        $this->villain = $villain;
    }

    public function addDealer(SpecialDealer $dealer): void
    {
        $this->dealer = $dealer;
    }

    public function addTable(SpecialTable $table): void
    {
        $this->table = $table;
    }

    public function addHandChecker(HandChecker $handChecker): void
    {
        $this->handChecker = $handChecker;
    }

    public function addModerator(Moderator $moderator): void
    {
        $this->moderator = $moderator;
    }

    public function addChallenge(Challenge $challenge): void
    {
        $this->challenge = $challenge;
    }

    public function getGameState(): array
    {
        $hero = $this->hero;
        $villain = $this->villain;
        $table = $this->table;

        return [
            "teddy_hand" => $villain->getImgPaths(),
            "mike_hand" => $hero->getImgPaths(),
            "teddy_stack" => $villain->getStack(),
            "mike_stack" => $hero->getStack(),
            "teddy_pos" => $villain->getPosition(),
            "mike_pos" => $hero->getPosition(),
            "pot_size" => $table->getPotSize(),
            "teddy_bet" => $villain->getCurrentBet(),
            "mike_bet" => $hero->getCurrentBet(),
            "price" => $table->getPriceToPlay(),
            "min_raise" => $table->getMinimumRaiseAllowed(),
            "board" => $table->getCardImages(),
            "street" => $table->getStreet(),
            "new_hand" => $this->newHand,
            "teddy_last_action" => $villain->getLastAction(),
            "winner" => $this->challenge->getHandWinner(),
            "teddy_hand_strength" => $villain->getStrength(),
            "mike_hand_strength" => $hero->getStrength(),
            "is_showdown" => $this->showdown,
            "game_over" => $this->gameOver,
            "result" => ($hero->getStack() - $hero->getStartStack()),
        ];
    }

    public function play($action)
    {
        // Check if challenge is over
        if ($this->isAllHandsPlayed()) {
            $this->gameOver = true;
            return;
        }

        // Check if a new hand is starting
        if ($this->newHand === true || $action === "next") {
            echo "setting up";
            $this->handSetUp();
        }

        // Check if any cards need to de dealt
        $this->dealCorrectStreet();

        // Hero could potentially make a play
        $this->heroAction($action);

        // Action is null when it is Villains turn to act
        // Villain always has the opportunity to act from the big blind
        if ($action === null && ($this->villain->getPosition() === "BB")|| $action ==="check") {
            $this->villainAction();
        }

        // Check if any cards need to de dealt after players have made their plays
        $this->dealCorrectStreet();
        if ($this->isSomeoneBroke()) {
            $this->gameOver = true;
        }

        // Check if all hands have been played
        if ($this->isAllHandsPlayed()) {
            $this->gameOver = true;
            return;
        }

        // Check if it is time for showdown
        if ($this->isShowdown()) {
            $this->showdown();
            return;
        }

    }

    public function isNewHand() : bool 
    {
        return $this->newHand;
    }

}