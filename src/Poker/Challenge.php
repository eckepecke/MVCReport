<?php

namespace App\Poker;

use App\Poker\Hero;
use App\Poker\Villain;

class Challenge
{
    private int $duration;
    private object $villain;
    private object $hero;
    private object $table;
    private object $dealer;


    private int $handsPlayed;

    public function __construct(int $hands)
    {
        $this->duration = $hands;
        $this->handsPlayed = 0;
    }

    public function addVillain(Villain $villain): void
    {
        $this->villain = $villain;
    }

    public function addHero(Hero $hero): void
    {
        $this->hero = $hero;
    }

    public function addTable(Table $table): void
    {
        $this->table = $table;
    }

    public function addDealer(Dealer $dealer): void
    {
        $this->dealer = $dealer;
    }

    public function incrementHandsPlayed(): void
    {
        $this->handsPlayed += 1;
    }

    public function challengeComplete(): bool
    {
        $done = false;
        $p1Stack = $this->hero->getStack();
        $p2Stack = $this->villain->getStack();

        if ($p1Stack <= 0 || $p2Stack <= 0 || $this->handsPlayed >= $this->duration) {
            $done = true;
        }
        return $done;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getHandsPlayed(): int
    {
        return $this->handsPlayed;
    }

    public function getHeroName(): string
    {
        return $this->hero->getName();
    }

    public function getVillainName(): string
    {
        return $this->villain->getName();
    }

    public function getResult(int $startingStack, int $currentStack): int
    {
        return ($currentStack - $startingStack);
    }

    public function betWasCalled()
    {
        $villainBet = $this->villain->getCurrentBet();
        $heroBet = $this->hero->getCurrentBet();
        $biggestBet = max($villainBet, $heroBet);
        $price = $this->table->getPriceToPlay();
        $caller = $this->hero;

        if ($biggestBet === $heroBet) {
            $caller = $this->villain;
        }
        $caller->call($price);

        $this->table->addChipsToPot($heroBet);
        $this->table->addChipsToPot($villainBet);
        $this->table->addChipsToPot($price);

        $this->villain->resetCurrentBet();
        $this->hero->resetCurrentBet();
    }

    public function villainUnOpenedPot($action)
    {
        switch ($action) {
            case "preflopRaise":
                echo "raise";
                $heroBet = $this->hero->getCurrentBet();
                $this->villain->raise($heroBet);
                break;

            case "preflopCall":
                echo "Call";
                $chipAmount = $this->table->getPriceToPlay();
                $this->villain->$action($chipAmount);
                break;

            default:
                echo "Fold";
                $this->villain->fold();
                $this->hero->muckCards();
                var_dump($this->table->getPotSize());
                $this->hero->takePot($this->table->getBlinds());
                $this->table->cleanTable();
                $this->incrementHandsPlayed();
        } 
    }

    public function assignHandStrengths($handChecker)
    {
        $board = $this->table->getBoard();
        $fullHeroHand = array_merge($this->hero->getHoleCards(), $board);
        $heroStrength = $handChecker->evaluateHand($fullHeroHand);
        $this->hero->updateStrength($heroStrength);

        $handChecker->resetStrengthArray();

        $fullVillainHand = array_merge($this->villain->getHoleCards(), $board);
        $villainStrength = $handChecker->evaluateHand($fullVillainHand);
        $this->villain->updateStrength($villainStrength);
    }
}
