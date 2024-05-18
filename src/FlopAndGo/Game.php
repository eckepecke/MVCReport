<?php

namespace App\FlopAndGo;

use App\FlopAndGo\Dealer;
use App\FlopAndGo\HandChecker;
use App\FlopAndGo\Hero;
use App\FlopAndGo\SpecialTable;
use App\FlopAndGo\Table;
use App\FlopAndGo\Villain;
use App\FlopAndGo\Managers\Manager;

class Game
{
    public object $hero;
    private object $villain;
    private object $dealer;
    private object $table;
    private object $handChecker;
    private object $challenge;
    private object $manager;

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

    public function addChallenge(Challenge $challenge): void
    {
        $this->challenge = $challenge;
    }

    public function addManager(Manager $manager): void
    {
        $this->manager = $manager;
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
            "new_hand" => $this->manager->newHandCheck(),
            "teddy_last_action" => $villain->getLastAction(),
            "winner" => $this->challenge->getHandWinner(),
            "teddy_hand_strength" => $villain->getStrength(),
            "mike_hand_strength" => $hero->getStrength(),
            "is_showdown" => $this->manager->isShowdown(),
            "game_over" => $this->manager->gameOverCheck(),
            "result" => ($hero->getStack() - $hero->getStartStack()),
        ];
    }

    public function play(mixed $action): void
    {
    $this->manager->allHandsHavePlayed();

    if ($this->manager->gameOverCheck()) {
        return;
    }
    

    $this->manager->setUpStreet($action);

    // Check if any cards need to de dealt
    $this->manager->dealCorrectStreet();

    $this->manager->villainPlay($action);

    // Hero could potentially make a play
    $this->manager->heroAction($action);

    // Check if any cards need to de dealt after players have made their plays
    $this->manager->dealCorrectStreet();


    $gameOver = $this->manager->isSomeoneBroke();


    // Check if all hands have been played

    $this->manager->allHandsHavePlayed();


    if ($this->manager->gameOverCheck()) {
        return;
    }

    // Check if it is time for showdown
    $this->manager->updateShowdownProp();
    
    if ($this->manager->isShowdown()) {
        $this->manager->showdown();
    return;
    }


    // Play again if someone folded before showdown
    if ($this->manager->newHandCheck()) {
        $this->play(null);
    }


        

    // Check if someone is broke
    // $this->manager->isSomeoneBroke();

    }

    public function getAllProperties(): array
    {
        return [
            'hero' => $this->hero,
            'villain' => $this->villain,
            'dealer' => $this->dealer,
            'table' => $this->table,
            'handChecker' => $this->handChecker,
            'challenge' => $this->challenge,
            'manager' => $this->manager,
        ];
    }
}
