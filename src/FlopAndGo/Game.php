<?php

namespace App\FlopAndGo;

use App\FlopAndGo\Dealer;
use App\FlopAndGo\HandChecker;
use App\FlopAndGo\Hero;
use App\FlopAndGo\Moderator;
use App\FlopAndGo\Table;
use App\FlopAndGo\Villain;

class Game
{
    private object $hero;
    private object $villain;
    private object $dealer;
    private object $table;
    private object $handChecker;
    private object $moderator;
    private object $challenge;


    public function addHero(Hero $hero): void
    {
        $this->hero = $hero;
    }

    public function addVillain(Villain $villain): void
    {
        $this->villain = $villain;
    }

    public function addDealer(Dealer $dealer): void
    {
        $this->dealer = $dealer;
    }

    public function addTable(Table $table): void
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
            "mos_hand" => $hero->getImgPaths(),
            "teddy_stack" => $villain->getStack(),
            "mos_stack" => $hero->getStack(),
            "teddy_pos" => $villain->getPosition(),
            "mos_pos" => $hero->getPosition(),
            "pot_size" => $table->getPotSize(),
            "teddy_bet" => $villain->getCurrentBet(),
            "mos_bet" => $hero->getCurrentBet(),
            "price" => $table->getPriceToPlay(),
            "min_raise" => $table->getMinimumRaiseAllowed(),
            "board" => $table->getCardImages(),
            "street" => $table->getStreet(),
            // "teddy_last_action" => $villain->getLastAction(),
            // "winner" => $this->challenge->getHandWinner(),
            // "teddy_hand_strength" => $villain->getStrength(),
            // "mos_hand_strength" => $hero->getStrength(),
        ];
    }

    public function play()
    {
        
    }
}