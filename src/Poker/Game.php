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
            "newHand" => true,
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
            "opponent2Stack" => $this->opponent2->getstack(),
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
        $PotManager = new PotManager();
        $PositionManager = new PositionManager();
        $cardManager = new CardManager();
        $betManager = new BetManager();
        $streetManager = new StreetManager();
        $heroActionManager = new HeroActionManager();
        $opponentActionManager = new OpponentActionManager();
        $stateManager = new StateManager();


        $PositionManager->assignPositions($pArray);

        // This is extended dealer class
        $cardManager->addDeck($deck);

        $manager->addCCM($CCManager);
        $manager->addPotManager($PotManager);
        $manager->addPositionManager($PositionManager);
        $manager->addCardManager($cardManager);
        $manager->addBetManager($betManager);
        $manager->addStreetManager($streetManager);
        $manager->addHeroActionManager($heroActionManager);
        $manager->addOpponentActionManager($opponentActionManager);
        $manager->addStateManager($stateManager);

        $manager->addGame($this);




        $this->addPlayers($pArray);
        $this->addDealer($cardManager);
        $this->addManager($manager);
        
    }

    public function play($action): void
    {
        $this->manager->dealStartingHands($this->getGameState());
        $this->newHand = false;
        $this->manager->dealCommunityCards($this->getGameState());
        $this->manager->playersMove($action, $this->getPlayers(), $this->getGameState());
        $this->manager->putChipsInPot();
        if($this->manager->handIsOver()) {
            $this->newHand = true;
            $this->manager->givePotToWinner();
            $this->manager->resetTable();
        }


    }
}
