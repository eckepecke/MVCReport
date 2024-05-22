<?php

namespace App\Poker;

use App\Poker\Player;
use App\Cards\DeckOfCards;
use App\Cards\CardHand;
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


// use App\Poker\ShowdownManager;









class Game
{
    private array $players;
    private object $dealer;
    private object $manager;
    private bool $newHand = true;
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
            "newHand" => $this->newHand,
            "hero" => $this->hero,
            "players" => $this->getPlayers(),

        ];
    }

    public function getTemplateData(): array
    {
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

        return [
            "heroHand" => $heroHand->getImgNames(),
            "opponent1Hand" => $opponent1Hand->getImgNames(),
            "opponent2Hand" => $opponent2Hand->getImgNames(),

            "heroBet" => $this->hero->getCurrentBet(),
            "opponent1Bet" => $this->opponent1->getCurrentBet(),
            "opponent2Bet" => $this->opponent2->getCurrentBet(),

            "heroStack" => $this->hero->getStack(),
            "opponent1Stack" => $this->opponent1->getStack(),
            "opponent2Stack" => $this->opponent2->getStack(),

            "hero_pos" => $this->hero->getPositionString(),
            "opp_1_pos" => $this->opponent1->getPositionString(),
            "opp_2_pos" => $this->opponent2->getPositionString(),

            "hero_last_action" => $this->hero->getLastAction(),
            "opp_1_last_action" => $this->opponent1->getLastAction(),
            "opp_2_last_action" => $this->opponent2->getLastAction(),

            "hero_active" => $this->hero->isActive(),
            "opp_1_active" => $this->opponent1->isActive(),
            "opp_2_active" => $this->opponent2->isActive(),

            "board" => $boardImages,
            "price" => $price,
            "min_raise" => $minRaise,
            "pot" => $pot,
            "new_hand" => $this->newHand,

        ];
    }

    public function init(): void
    {

        $player1 = new Player();
        $player1->setHero();
        $player2 = new Opponent();
        $player3 = new Opponent();
        $pArray = [
            $player1,
            $player2,
            $player3
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

        // $showdownManager = new ShowdownManager();



        $positionManager->assignPositions($pArray);

        // This is extended dealer class
        $cardManager->addDeck($deck);

        // $manager->addCCM($CCManager);
        // $manager->addPotManager($PotManager);
        // $manager->addPositionManager($PositionManager);
        // $manager->addCardManager($cardManager);
        // $manager->addBetManager($betManager);
        // $manager->addStreetManager($streetManager);
        // $manager->addHeroActionManager($heroActionManager);
        // $manager->addOpponentActionManager($opponentActionManager);
        // $manager->addStateManager($stateManager);

        $manager->addManager('CCManager', $CCManager);
        $manager->addManager('potManager', $potManager);
        $manager->addManager('positionManager', $positionManager);
        $manager->addManager('cardManager', $cardManager);
        $manager->addManager('betManager', $betManager);
        $manager->addManager('streetManager', $streetManager);
        $manager->addManager('heroActionManager', $heroActionManager);
        $manager->addManager('opponentActionManager', $opponentActionManager);
        $manager->addManager('stateManager', $stateManager);

        // $manager->addShowdownManager($showdownManager);


        $manager->addGame($this);




        $this->addPlayers($pArray);
        $this->addDealer($cardManager);
        $this->addManager($manager);
        
    }

    public function play($heroAction): void
    {
        // if ($this->manager->isShowdown()) {
        //     echo "showdown!";
        //     var_dump($sdcrash);
        // }

        if ($this->manager->handIsOver()) {
            $this->newHand = true;
            $this->manager->givePotToWinner();
            $this->manager->resetTable();
        }

        $this->manager->dealStartingHands($this->getGameState(), $heroAction);
        $this->newHand = false;
        $this->manager->dealCommunityCards($this->getGameState());
        

        $this->manager->playersAct($heroAction, $this->getGameState());

        if ($this->manager->everyoneMoved($heroAction)) {
            $this->manager->handleChips();
            $this->manager->updateStreet($heroAction);
            $this->manager->dealCommunityCards($this->getGameState());
            if ($this->manager->isShowdown()) {
                echo "showdown!";
                var_dump($sdcrash);
            }
        }
    }

    public function resetNewHand(): void
    {
        $this->newHand = true;
    }

    public function heroInput($action) {
        $this->manager->heroAction($action, $this->hero);
    }
}
