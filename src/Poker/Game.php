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





class Game
{
    private array $players;
    private object $dealer;
    private object $manager;
    private bool $newHand = true;

    public function addPlayers(array $players): void
    {
        foreach ($players as $player) {
            $this->players[] = $player;
        }
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
        $player1Hand = $players[0]->getHand();
        $player2Hand = $players[1]->getHand();
        $player3Hand = $players[2]->getHand();

        $CCM = $this->manager->access("CCManager");
        $board = $CCM->getBoard();
        $boardImages = [];
        foreach ($board as $card) {
            $boardImages[] = $card->getImgName();
        }


        return [
            "player1Hand" => $player1Hand->getImgNames(),
            "player2Hand" => $player2Hand->getImgNames(),
            "player3Hand" => $player3Hand->getImgNames(),
            "board" => $boardImages,
        ];
    }

    public function init(): void
    {

        $player1 = new Player();
        $player2 = new Player();
        $player3 = new Player();
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


        // This is extended dealer class
        $cardManager->addDeck($deck);

        $manager->addCCM($CCManager);
        $manager->addPotManager($PotManager);
        $manager->addPositionManager($PositionManager);
        $manager->addCardManager($cardManager);
        $manager->addBetManager($betManager);
        $manager->addStreetManager($streetManager);

        $this->addPlayers($pArray);
        $this->addDealer($cardManager);
        $this->addManager($manager);
    }

    public function play(): void
    {
        $this->manager->dealStartingHands($this->getGameState());
        $this->newHand = false;
        $this->manager->dealCommunityCards($this->getGameState());
    }
}
