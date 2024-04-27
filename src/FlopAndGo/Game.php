<?php

namespace App\Poker;

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
        $this->dealer = $dealer;
    }

    public function addHandChecker(HandChecker $handChecker): void
    {
        $this->handChecker = $handChecker;
    }

    public function addModerator(Moderator $moderator): void
    {
        $this->moderator = $moderator;
    }

    public function play()
    {
        
    }
}