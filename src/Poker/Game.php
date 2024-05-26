<?php

namespace App\Poker;

use App\Poker\Player;
use App\Cards\DeckOfCards;
use App\Poker\CardHand;
use App\Poker\Manager;
use App\Poker\CommunityCardManager;
use App\Poker\PotManager;
use App\Poker\PositionManager;
use App\Poker\CardManager;
use App\Poker\BetManager;
use App\Poker\StreetManager;
use App\Poker\HeroActionManager;
use App\Poker\OpponentActionManager;
use App\Poker\StateManager;
use App\Poker\ShowdownManager;
use App\Poker\HandEvaluator;


class Game
{
    private array $players;
    private object $dealer;
    private object $manager;
    // private bool $newHand = true;
    private bool $preflop = true;


    private object $hero;
    private object $opponent1;
    private object $opponent2;




    public function addPlayers(array $players): void
    {
        foreach ($players as $player) {
            $this->players[] = $player;
        }

        $this->hero = $players[0];
        $this->opponent1 = $players[1];
        $this->opponent2 = $players[2];

    }

    public function addDealer(cardManager $dealer): void
    {
        $this->dealer = $dealer;
    }

    public function addManager(Manager $manager): void
    {
        $this->manager = $manager;
    }


    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getGameState(): array
    {
        return [
            // "newHand" => $this->newHand,
            "preflop" => $this->preflop,

            "hero" => $this->hero,
            "players" => $this->getPlayers(),
            "active" => $this->manager->access("stateManager")->removeInactive($this->players),

            // "phase" => $this->phase,

        ];
    }

    public function getTemplateData(): array
    {
        $newHand = $this->manager->access("stateManager")->getNewHand();
        $players = $this->getPlayers();

        $heroHand = $this->hero->getHand();
        $opponent1Hand = $this->opponent1->getHand();
        $opponent2Hand = $this->opponent2->getHand();

        $state = $this->getGameState();

        $CCM = $this->manager->access("CCManager");
        $board = $CCM->getBoard();
        $boardImages = [];
        foreach ($board as $card) {
            $boardImages[] = $card->getImgName();
        }

        $BM = $this->manager->access("betManager");
        $price = $BM->getPriceToPlay($state);
        $minRaise = $BM->getMinimumRaiseAllowed($state);

        $PM = $this->manager->access("potManager");
        $pot = $PM->getPotSize($state);

        $SDM = $this->manager->access("showdownManager");
        $winner = $SDM->getWinner();
        $winnerName = null;
        if($winner != null) {
            $winnerName = $winner->getName();
        }
        return [
            "hero_hand" => $heroHand->getImgNames(),
            "opponent1Hand" => $opponent1Hand->getImgNames(),
            "opponent2Hand" => $opponent2Hand->getImgNames(),

            "heroBet" => $this->hero->getCurrentBet(),
            "opponent1Bet" => $this->opponent1->getCurrentBet(),
            "opponent2Bet" => $this->opponent2->getCurrentBet(),

            "hero_stack" => $this->hero->getStack(),
            "opponent_1_stack" => $this->opponent1->getStack(),
            "opponent_2_stack" => $this->opponent2->getStack(),

            "hero_pos" => $this->hero->getPositionString(),
            "opp_1_pos" => $this->opponent1->getPositionString(),
            "opp_2_pos" => $this->opponent2->getPositionString(),

            "hero_last_action" => $this->hero->getLastAction(),
            "opp_1_last_action" => $this->opponent1->getLastAction(),
            "opp_2_last_action" => $this->opponent2->getLastAction(),

            "hero_active" => $this->hero->isActive(),
            "opp_1_active" => $this->opponent1->isActive(),
            "opp_2_active" => $this->opponent2->isActive(),

            "hero_strength" => $heroHand->getStrengthString(),
            "opp_1_strength" => $opponent1Hand->getStrengthString(),
            "opp_2_strength" => $opponent2Hand->getStrengthString(),


            "board" => $boardImages,
            "price" => $price,
            "min_raise" => $minRaise,
            "pot" => $pot,
            "new_hand" => $newHand,
            "showdown" => $this->manager->isShowdown(),
            "winner" => $winnerName,
        ];
    }

    public function init(): void
    {

        $player1 = new Player();
        $player1->setName("Hero");
        $player1->setHero();
        $player2 = new Opponent();
        $player2->setName("Isildur1");
        $player3 = new Opponent();
        $player3->setName("Phil");
        $pArray = [
            $player1,
            $player2,
            $player3,

        ];



        $deck = new DeckOfCards();
        $manager = new Manager();
        $CCManager = new CommunityCardManager();
        $potManager = new PotManager();
        $positionManager = new PositionManager();
        $cardManager = new CardManager();
        $betManager = new BetManager();
        $streetManager = new StreetManager();
        $heroActionManager = new HeroActionManager();
        $opponentActionManager = new OpponentActionManager();
        $stateManager = new StateManager();
        $showdownManager = new ShowdownManager();

        $handEvaluator = new HandEvaluator();


        $positionManager->assignPositions($pArray);

        // This is extended dealer class
        $cardManager->addDeck($deck);
        $cardManager->addEvaluator($handEvaluator);

        $manager->addManager('CCManager', $CCManager);
        $manager->addManager('potManager', $potManager);
        $manager->addManager('positionManager', $positionManager);
        $manager->addManager('cardManager', $cardManager);
        $manager->addManager('betManager', $betManager);
        $manager->addManager('streetManager', $streetManager);
        $manager->addManager('heroActionManager', $heroActionManager);
        $manager->addManager('opponentActionManager', $opponentActionManager);
        $manager->addManager('stateManager', $stateManager);
        $manager->addManager('showdownManager', $showdownManager);

        $manager->addGame($this);

        $this->addPlayers($pArray);
        $this->addDealer($cardManager);
        $this->addManager($manager);
        //debug
        $this->manager->access("positionManager")->updatePositions($this->players);
    }

    public function play($heroAction): void
    {
        var_dump($this->manager->access("stateManager")->getNewHand());
        if ($this->manager->access("stateManager")->getNewHand()) {
                        // FOR DEBUGGING
            // $this->manager->access("positionManager")->updatePositions($this->players);

            echo "NEW HAND STARTING";
            $this->manager->resetTable($this->players);
            $this->manager->dealStartingHands($this->getGameState(), $heroAction);
            $this->manager->updatePlayersCurrentHandStrength($this->players);
            $this->manager->access("stateManager")->setNewHand(false);
        }

        $this->postflop($heroAction);

    }

    // public function preflop($heroAction): void
    // {
    //     // if ($this->manager->newHandStarting($heroAction)) {
    //     //     echo "NEW HAND STARTING";
    //     //     $this->newHand = true;
    //     //     $this->manager->givePotToWinner();
    //     //     $this->manager->resetTable($this->players);
    //     // }



    //     echo"PREFLOPACTION";
    //     $this->manager->preflopRevised($heroAction, $this->getGameState());

        // if (!$this->manager->access("streetManager")->isPreflop()) {
        //     echo"PREFLOP ENDS!";
        //     $this->manager->access("potManager")->addChipsToPot($this->getGameState());
        //     $this->manager->access("betManager")->resetPlayerBets($this->players);
        //     $this->manager->access("streetManager")->setNextStreet();
        //     $heroAction = null;
        //     $this->postflop($heroAction);


            // $this->manager->handleChips($this->getGameState());
        // }


        //$this->manager->updatePlayersCurrentHandStrength($this->players);


    // }

    public function resetNewHand(): void
    {
        $this->newHand = true;
    }

    public function postflop($heroAction)
    {
        echo"POSTFLOPSTART";

        $this->manager->postFlopRevised($heroAction, $this->getGameState());

        if ($this->manager->isShowdown()) {
            echo "showdown!";
            $this->manager->showdown($this->players);
            $this->manager->givePotToShowdownWinner();

            return;
        }
    }
}
