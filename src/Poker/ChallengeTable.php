<?php

namespace App\Poker;

use App\Poker\Hero;
use App\Poker\Villain;
use App\Poker\Challenge;
use App\Poker\Dealer;
use App\Poker\ChallengeDealer;
use App\Poker\Table;



use App\Cards\CardGraphic;
use App\Cards\CardHand;

use App\Cards\DeckOfCards;

class ChallengeTable extends Table
{
    private int $bigBlind;
    private int $smallBlind;
    private object $dealer;
    private object $sbPlayer;
    private object $bbPlayer;

    public function __construct(int $small, int $big)
    {
        parent::__construct();
        $this->smallBlind = $small;
        $this->bigBlind = $big;
    }

    public function seatDealer(ChallengeDealer $dealer): void
    {
        $this->dealer = $dealer;
    }

    public function seatPlayers(object $player1, object $player2): void
    {
        $this->sbPlayer = $player1;
        $this->sbPlayer->setPosition("SB");

        $this->bbPlayer = $player2;
        $this->bbPlayer->setPosition("BB");

    }

    public function getSmallBlind(): int
    {
        return $this->smallBlind;
    }

    public function getBigBlind(): int
    {
        return $this->bigBlind;
    }

    public function moveButton(): void
    {
        $temp = $this->sbPlayer;
        $this->sbPlayer = $this->bbPlayer;
        $this->bbPlayer = $temp;

        $this->bbPlayer->setPosition("BB");
        $this->sbPlayer->setPosition("SB");
    }

    public function getSbPlayer(): object
    {
        return $this->sbPlayer;
    }

    public function getBbPlayer(): object
    {
        return $this->bbPlayer;
    }

    public function chargeAntes(): void
    {
        $this->sbPlayer->payBlind($this->smallBlind);
        $this->bbPlayer->payBlind($this->bigBlind);
        $this->sbPlayer->setCurrentBet($this->smallBlind);
        $this->bbPlayer->SetCurrentBet($this->bigBlind);
    }

    public function getPriceToPlay(): int
    {
        $amountOne = $this->bbPlayer->getCurrentBet();
        $amountTwo = $this->sbPlayer->getCurrentBet();

        $biggestAmount = max($amountOne, $amountTwo);
        $smallestAmount = min($amountOne, $amountTwo);

        return $biggestAmount - $smallestAmount;
    }

    public function getMinimumRaiseAllowed(): int
    {
        $amountOne = $this->bbPlayer->getCurrentBet();
        $amountTwo = $this->sbPlayer->getCurrentBet();

        $biggestAmount = max($amountOne, $amountTwo);

        return 2 * $biggestAmount;
    }

    public function getCardImages(): array
    {
        $imgPaths = [];
        foreach ($this->fullBoard as $card) {
            $imgPath = $card->getImgName();
            $imgPaths[] = $imgPath;
        }

        return $imgPaths;
    }

    public function getBlinds() : int
    {
        return $this->smallBlind + $this->bigBlind; 
    }

    public function collectUnraisedPot(): void
    {
        $this->addChipsToPot($this->getBigBlind());
        $this->addChipsToPot($this->getBigBlind());
        $this->sbPlayer->resetCurrentBet();
        $this->bbPlayer->resetCurrentBet();
    }

    public function dealCorrectStreet(string $heroPos, int $street): void
    {
        if ($heroPos === "SB" || ($heroPos === "BB" && $street === 1)) {
            echo"activated";
            var_dump($this->street);
            $this->incrementStreet();
            var_dump($this->street);

        }

        $street = $this->street;

        if ($street === 2 && ($this->flop === [])) {
            echo"activated 3";

            $flop = $this->dealer->dealFlop();
            $this->registerMany($flop);
        }

        if ($street >= 3 && (count($this->fullBoard) < 5) && $heroPos === "SB") {
            echo"activated 4";

            $card = $this->dealer->dealOne();
            $this->registerOne($card);
        }

    }

    public function dealCorrectCardAfterCall()
    {
        $street = $this->street;
        
        if ($street === 1) {
            $flop = $this->dealer->dealFlop();
            $this->registerMany($flop);
            $this->incrementStreet();
        }

        if ($street === 2) {
            $turn = $this->dealer->dealOne();
            $this->registerOne($turn);
            $this->incrementStreet();
        }

        if ($street === 3) {
            $river = $this->dealer->dealOne();
            $this->registerOne($river);
            $this->incrementStreet();
        }
    }
}
