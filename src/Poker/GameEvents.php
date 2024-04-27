<?php

namespace App\Poker;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GameEvents extends Game
{
    public function preflopPrep()
    {
        $this->table->moveButton();
        $this->deck->initializeCards();
        $this->deck->shuffleDeck();
        //need to turn these in to small blinds
        $this->table->chargeAntes(25, 50);
        $this->dealer->dealHoleCards();
    }

    public function someoneFolded()
    {
        $this->dealer->moveChipsAfterFold();
        $this->dealer->resetForNextHand();
        $this->challenge->incrementHandsPlayed();
    }

    public function heroChecked()
    {
        $heroPos = $this->hero->getPosition();
        $street = $this->table->getStreet();

        if (($heroPos === "BB" && $street === 1 && $this->table->getFlop() === [])) {
            //Adding chips when hero checks back preflop
            $this->table->collectUnraisedPot();
        }

        $this->table->dealCorrectStreet($heroPos);
        $street = $this->table->getStreet();

        if ($this->villain->getPosition() === "SB") {
            $action = $this->villain->actionVsCheck();
            $action = "check";
            if ($action === "check") {
                if ($street <= 4 && $street > 1 && count($this->table->getBoard()) < 5) {
                    $card = $this->dealer->dealOne();
                    $this->table->registerOne($card);
                    var_dump($this->table->getStreet());
                }
                // if ($this->table->getStreet() >= 4) {
                echo "I am in herochecked";
                //     ////Ineed to redirect route here
                //     var_dump($this->table->getStreet());

                //     $this->compareHands();
                // }
                $this->table->incrementStreet();
                var_dump($this->table->getStreet());



            }
            if ($action === "bet") {
                $betSize = $this->villain->betVsCheck($this->table->getPotSize());
                $this->villain->bet($betSize);
            }
        }
    }

    public function compareHands()
    {
        $this->assignHandStrengths();
        $winner = $this->handChecker->compareStrength($this->hero, $this->villain);
        $winner->takePot($this->table->getPotsize());
        $this->challenge->setHandWinner($winner->getName());
        $this->table->incrementStreet();
        echo"I am in comapare hand";
        var_dump($this->table->getStreet());

        // $session->set("winner", $this->challenge->getHandWinner());
        // $session->set("teddy_hand_strength", $this->villain->getStrength());
        // $session->set("mos_hand_strength", $this->hero->getStrength());
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

    public function assignHandStrengths()
    {
        $board = $this->table->getBoard();
        $fullHeroHand = array_merge($this->hero->getHoleCards(), $board);

        $heroStrength = $this->handChecker->evaluateHand($fullHeroHand);
        $this->hero->updateStrength($heroStrength);

        $this->handChecker->resetStrengthArray();

        $fullVillainHand = array_merge($this->villain->getHoleCards(), $board);
        $villainStrength = $this->handChecker->evaluateHand($fullVillainHand);
        $this->villain->updateStrength($villainStrength);
    }

    public function villainCouldBetFromBigBlind()
    {
        if ($this->villain->getPosition() === "BB") {
            // $this->villainCouldBetFromBigBlind();
            $action = $this->villain->postFlopBetOpportunity();
            if ($action === "bet") {
                echo "Villain bettar";
                $betSize = $this->villain->betVsCheck($this->table->getPotSize());
                $this->villain->bet($betSize);
            }
        }
    }
}
