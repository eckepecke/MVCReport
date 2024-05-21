<?php

namespace App\Poker;

// use App\Poker\CommunityCardManager;
// use App\Poker\PotManager;
// use App\Poker\PositionManager;


/**
 * Manages the game.
 */
class Manager
{
    private object $game;
    private object $CCManager;
    private object $potManager;
    private object $positionManager;
    private object $cardManager;
    private object $betManager;
    private object $streetManager;
    private object $heroActionManager;
    private object $opponentActionManager;
    private object $stateManager;



    public function addGame(Game $game): void
    {
        $this->game = $game;
    }

    public function addCCM(CommunityCardManager $manager): void
    {
        $this->CCManager = $manager;
    }

    public function addPotManager(PotManager $manager): void
    {
        $this->potManager = $manager;
    }

    public function addPositionManager(PositionManager $manager): void
    {
        $this->positionManager = $manager;
    }

    public function addCardManager(CardManager $manager): void
    {
        $this->cardManager = $manager;
    }

    public function addBetManager(BetManager $manager): void
    {
        $this->betManager = $manager;
    }

    public function addStreetManager(StreetManager $manager): void
    {
        $this->streetManager = $manager;
    }

    public function addHeroActionManager(HeroActionManager $manager): void
    {
        $this->heroActionManager = $manager;
    }

    public function addOpponentActionManager(OpponentActionManager $manager): void
    {
        $this->opponentActionManager = $manager;
    }

    public function addStateManager(StateManager $manager): void
    {
        $this->stateManager = $manager;
    }

    public function access(string $manager): object
    {
        return $this->$manager;
    }


    public function dealStartingHands($state)
    {
        if($state["newHand"] === true) {
            $this->cardManager->shuffleCards();
            $this->cardManager->dealStartHandToAllPlayers($state["players"]);
        }
    }

    public function dealCommunityCards($state)
    {
        // Will price check be enough in the future?
        // ?????????????????????????????????????
        $priceToPlay = $this->betManager->getPriceToPlay($state);
        if ($priceToPlay === 0) {
            $street = $this->streetManager->getStreet();
            $cardsDealt = $this->CCManager->cardsDealt();
            $cards = $this->cardManager->dealCommunityCards($street, $cardsDealt);
            $this->CCManager->register($cards);
        }
    }

    public function playersMoveTest(mixed $action, array $players): void
    {
        echo "test";

        var_dump($action);
        if ($action != null && $action != "next") {
            echo "hello";
            $this->positionManager->sortPlayersByPosition($players);
            foreach ($players as $player) {
                if ($player->isHero()) {
                    continue;
                }
                $priceToPlay = $this->betManager->getPriceToPlay($this->game->getGameState());
                $this->opponentActionManager->move($priceToPlay, $player);
            }
        }
        
    }

    public function opponentsMove(mixed $action, array $players): void
    {
        $this->positionManager->sortPlayersByPosition($players);

        foreach ($players as $player) {
            if ($player->isHero()) {
                continue;
            }
            $priceToPlay = $this->betManager->getPriceToPlay($this->game->getGameState());
            $this->opponentActionManager->move($priceToPlay, $player);
        }
    }

    public function heroAction(mixed $action, object $player): void
    {
        $this->heroActionManager->heroMove($action, $player);
    }

    public function putChipsInPot(): void
    {
        $state = $this->game->getGameState();
        $priceToPlay = $this->betManager->getPriceToPlay($state);
        $activePlayers = $this->stateManager->getActivePlayers($state);

        // Adding chips to pot when there is no uncalled bet
        // or if there is only one active player (rest folded).
        if ($priceToPlay === 0 || $activePlayers < 2) {
            $this->potManager->addChipsToPot($state);
        }
    }

    public function handIsOver(): bool
    {
        $state = $this->game->getGameState();
        $activePlayers = $this->stateManager->getActivePlayers($state);

        if ($activePlayers < 2) {
            return true;
        }
        return false;
    }

    public function givePotToWinner(): void
    {
        $state = $this->game->getGameState();
        $winner = $this->stateManager->getWinner($state);
        $pot = $this->potManager->getPotSize();
        $winner->takePot($pot);
    }

    public function resetTable(): void
    {
        $this->potManager->emptyPot();
        // $players = $this->game->getPlayers();
        // $this->cardManager->resetPlayerHands($players);
        $this->CCManager->resetBoard();
    }

    public function updateStreet($action): void
    {
        $state = $this->game->getGameState();
        $activePlayers = $this->stateManager->getActivePlayers($state);
        $priceToPlay = $this->betManager->getPriceToPlay($state);


        if ($activePlayers > 1 && $action != null && $action != "next" && $priceToPlay === 0) {
            $this->streetManager->setNextStreet();
        }
    }
}