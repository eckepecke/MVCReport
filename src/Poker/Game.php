<?php

namespace App\Poker;

use App\Poker\Player;
use App\Poker\Hero;

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
use App\Poker\SameHandEvaluator;
use App\Poker\GameOverTracker;

class Game
{
    private array $players;
    private object $manager;
    private bool $gameOver = false;


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
            "hero" => $this->hero,
            "players" => $this->getPlayers(),
            "active" => $this->manager->access("stateManager")->removeInactive($this->players),
            "game_over" => $this->gameOver,
        ];
    }

    public function getTemplateData(): array
    {
        $newHand = $this->manager->access("stateManager")->getNewHand();

        $heroHand = $this->hero->getHand();
        $opponent1Hand = $this->opponent1->getHand();
        $opponent2Hand = $this->opponent2->getHand();

        $state = $this->getGameState();

        $cCManager = $this->manager->access("CCManager");
        $board = $cCManager->getBoard();
        $boardImages = [];
        foreach ($board as $card) {
            $boardImages[] = $card->getImgName();
        }

        $betManager = $this->manager->access("betManager");
        $price = $betManager->getPriceToPlay($state);
        $minRaise = $betManager->getMinimumRaiseAllowed($state);

        $potManager = $this->manager->access("potManager");
        $pot = $potManager->getPotSize($state);

        $showdownManager = $this->manager->access("showdownManager");
        $winner = $this->manager->access("showdownManager")->getWinner();
        $winnerName = null;
        if($winner != null) {
            $winnerName = $winner->getName();
        }
        return [
            "hero_hand" => $heroHand->getImgNames(),
            "opponent1Hand" => $opponent1Hand->getImgNames(),
            "opponent2Hand" => $opponent2Hand->getImgNames(),

            "hero_name" => $this->hero->getName(),
            "opp_1_name" => $this->opponent1->getName(),
            "opp_2_name" => $this->opponent2->getName(),

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
            "showdown" => $this->manager->access("streetManager")->getShowdown(),
            "winner" => $winnerName,

            "allin" => $this->hero->isAllin(),

        ];
    }

    public function init(): void
    {

        $player1 = new Hero();
        $player1->setName("Hero");
        $this->hero = $player1;
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
        $cCManager = new CommunityCardManager();
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
        $sameHandEvaluator = new SameHandEvaluator();
        $gameOverTracker = new GameOverTracker(1);

        $showdownManager->add($sameHandEvaluator);


        $positionManager->assignPositions($pArray);

        // This is extended dealer class
        $cardManager->addDeck($deck);
        $cardManager->addEvaluator($handEvaluator);

        $manager->addManager('CCManager', $cCManager);
        $manager->addManager('potManager', $potManager);
        $manager->addManager('positionManager', $positionManager);
        $manager->addManager('cardManager', $cardManager);
        $manager->addManager('betManager', $betManager);
        $manager->addManager('streetManager', $streetManager);
        $manager->addManager('heroActionManager', $heroActionManager);
        $manager->addManager('opponentActionManager', $opponentActionManager);
        $manager->addManager('stateManager', $stateManager);
        $manager->addManager('showdownManager', $showdownManager);
        $manager->addManager('gameOverTracker', $gameOverTracker);

        // $manager->addGame($this);

        $this->addPlayers($pArray);
        $this->addManager($manager);
        //debug
        $this->manager->access("positionManager")->updatePositions($this->players);
    }

    public function prepare($heroAction): void
    {






        if ($this->manager->access("stateManager")->getNewHand()) {


            echo "NEW HAND STARTING";

            $this->manager->resetTable($this->players);
            $allHandsPlayed = $this->manager->access("gameOverTracker")->allHandsPlayed();
            if (($this->hero->getStack() <= 0) || $allHandsPlayed) {
                $this->gameOver = true;
                var_dump($crash);
            }

            $this->manager->access("potManager")->chargeBlinds($this->players);

            $this->manager->access("cardManager")->dealStartingHands($this->players);
            $this->manager->updatePlayersCurrentHandStrength($this->players);
            $this->manager->access("stateManager")->setNewHand(false);
            $this->manager->access("streetManager")->setShowdownFalse();
            $this->manager->access("streetManager")->resetStreet();
            $this->manager->access("betManager")->setActionIsClosed(false);


        }









        $this->play($heroAction);

    }

    public function resetNewHand(): void
    {
        $this->newHand = true;
    }

    public function play($heroAction)
    {
        echo"POSTFLOPSTART";

        $this->manager->heroMakesAPlay($heroAction, $this->getGameState());
        $this->manager->opponentsPlay($heroAction, $this->getGameState());
        $endBeforeSHowdown = $this->manager->access("stateManager")->getNewHand();

        if ($endBeforeSHowdown) {
            $this->manager->givePotToWinner($this->getGameState());
            $this->manager->access("gameOverTracker")->incrementHands();
            $this->manager->access("stateManager")->setNewHand(true);

        }

        $activePlayers = $this->manager->access("stateManager")->removeInactive($this->players);
        if ($this->hero->isAllin() && $activePlayers > 1) {
            $this->manager->access("potManager")->addChipsToPot($this->getGameState());
            $this->manager->dealToShowDown();
            $this->manager->access("streetManager")->setShowdownTrue();
        }

        if ($this->manager->access("streetManager")->getShowdown()) {
            echo "showdown!";
            $this->manager->showdown($this->players);
            $this->manager->access("stateManager")->setNewHand(true);
            $this->manager->access("gameOverTracker")->incrementHands();
        }
    }
}
