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
    // private object $CCManager;
    // private object $potManager;
    // private object $positionManager;
    // private object $cardManager;
    // private object $betManager;
    // private object $streetManager;
    // private object $heroActionManager;
    // private object $opponentActionManager;
    // private object $stateManager;

    private array $managers = [];



    // private object $showdownManager;




    public function addGame(Game $game): void
    {
        $this->game = $game;
    }

    public function addManager(string $key, object $manager): void
    {
        $this->managers[$key] = $manager;
    }

    // public function addCCM(CommunityCardManager $manager): void
    // {
    //     $this->CCManager = $manager;
    // }

    // public function addPotManager(PotManager $manager): void
    // {
    //     $this->potManager = $manager;
    // }

    // public function addPositionManager(PositionManager $manager): void
    // {
    //     $this->positionManager = $manager;
    // }

    // public function addCardManager(CardManager $manager): void
    // {
    //     $this->cardManager = $manager;
    // }

    // public function addBetManager(BetManager $manager): void
    // {
    //     $this->betManager = $manager;
    // }

    // public function addStreetManager(StreetManager $manager): void
    // {
    //     $this->streetManager = $manager;
    // }

    // public function addHeroActionManager(HeroActionManager $manager): void
    // {
    //     $this->heroActionManager = $manager;
    // }

    // public function addOpponentActionManager(OpponentActionManager $manager): void
    // {
    //     $this->opponentActionManager = $manager;
    // }

    // public function addStateManager(StateManager $manager): void
    // {
    //     $this->stateManager = $manager;
    // }


    // public function addShowdownManager(ShowdownManager $manager): void
    // {
    //     $this->showdownManager = $manager;
    // }

    public function access(string $manager): object
    {
        return $this->managers[$manager];
    }

    public function dealStartingHands($state)
    {
        if($state["newHand"] === true) {
            $this->managers["cardManager"]->shuffleCards();
            $this->managers["cardManager"]->dealStartHandToAllPlayers($state["players"]);
        }
    }

    public function dealCommunityCards($state)
    {
        // Will price check be enough in the future?
        // ?????????????????????????????????????
        $players = $this->game->getPlayers();
        // $activePlayers = $this->stateManager->removeInactive($state);

        $priceToPlay = $this->managers["betManager"]->getPriceToPlay($state);
        if ($priceToPlay === 0) {
            $street = $this->managers["streetManager"]->getStreet();
            $cardsDealt = $this->managers["CCManager"]->cardsDealt();
            $cards = $this->managers["cardManager"]->dealCommunityCards($street, $cardsDealt);
            $this->managers["CCManager"]->register($cards);
        }
    }

    public function playersAct(mixed $heroAction, array $state): void
    {
        // Either Hero has => Let everyone behind him move.
        // When he did not already move => Let everyone in front move
        // until hero' turn.
        if ($this->managers["stateManager"]->heroAlreadyMoved($heroAction)) {
            $this->heroAction($heroAction, $state["hero"]);
            // Prevent opponents from acting if hero closed the betting round.
            if ($this->managers["betManager"]->playerClosedAction($state["hero"], $state)) {
                return;
            }

            $this->opponentsBehindMove($state);
            // Now everyone should have made a play,
            // register that to stateManager.
            $this->managers["stateManager"]->everyoneMoved();
        } else {
            $this->playUntilHeroTurn($state);
        }
    }

    public function playUntilHeroTurn(array $state)
    {    
        echo "playUntilHeroTurn()";
        $players = $state["players"];
            $players = $this->managers["positionManager"]->sortPlayersByPosition($players);
            foreach ($players as $player) {
                if ($player->isHero() && $player->isActive()) {
                    echo "HeroToAct";
                    return;
                    }

                }
                if ($player->isActive()) {
                    $priceToPlay = $this->managers["betManager"]->getPriceToPlay($this->game->getGameState());
                    $potSize = $this->managers["potManager"]->getPotSize();
                    $currentBiggestBet = $this->managers["betManager"]->getBiggestBet($this->game->getGameState());

                    $this->managers["opponentActionManager"]->move($priceToPlay, $player, $potSize, $currentBiggestBet);
                    // return early if player closed the betting round.
                    if ($this->managers["betManager"]->playerClosedAction($player, $state)) {
                        return;
                    }
                }
    }


    public function opponentsBehindMove(array $state): void
    {
        echo "opponentsMove()";
        $hero = $state["hero"];
        $heroPos = $hero->getPosition();

        // $this->positionManager->sortPlayersByPosition($players);
        $players = $state["players"];
        foreach ($players as $player) {
            if ($player->isHero()) {
                echo "skip";
                continue;
            }
            $currentPosition = $player->getPosition();
            if ($player->isActive() && $currentPosition > $heroPos) {
                "echo IP PLayer makes move";
                $priceToPlay = $this->managers["betManager"]->getPriceToPlay($this->game->getGameState());
                $potSize = $this->managers["potManager"]->getPotSize();
                $currentBiggestBet = $this->managers["betManager"]->getBiggestBet($this->game->getGameState());

                $this->managers["opponentActionManager"]->move($priceToPlay, $player, $potSize, $currentBiggestBet);
                // return early if player closed the betting round.
                if ($this->managers["betManager"]->playerClosedAction($player, $state)) {
                    return;
                }
            }
        }
        // Now everyone should have made a play
    }

    public function heroAction(mixed $action, object $player): void
    {
        // $priceToPlay = $this->betManager->getPriceToPlay($this->game->getGameState());
        $currentBiggestBet = $this->managers["betManager"]->getBiggestBet($this->game->getGameState());


        $this->managers["heroActionManager"]->heroMove($action, $player, $currentBiggestBet);
    }

    public function handleChips(): void
    {
        $state = $this->game->getGameState();
        $players = $state["players"];
        $priceToPlay = $this->managers["betManager"]->getPriceToPlay($state);
        $activePlayers = $this->managers["stateManager"]->getActivePlayers($state);

        // Adding chips to pot when there is no uncalled bet
        // or if there is only one active player (rest folded).
        if ($priceToPlay === 0 || $activePlayers < 2) {
            $this->managers["potManager"]->addChipsToPot($state);
            $this->managers["betManager"]->resetPlayerBets($players);
        }

    }

    public function handIsOver(): bool
    {
        $state = $this->game->getGameState();
        $activePlayers = $this->managers["stateManager"]->getActivePlayers($state);

        if ($activePlayers < 2) {
            return true;
        }
        return false;
    }

    public function givePotToWinner(): void
    {
        $state = $this->game->getGameState();
        $winner = $this->managers["stateManager"]->getWinner($state);
        $pot = $this->managers["potManager"]->getPotSize();
        $winner->takePot($pot);
    }

    public function resetTable(): void
    {
        $this->managers["potManager"]->emptyPot();
        // $players = $this->game->getPlayers();
        // $this->cardManager->resetPlayerHands($players);
        $this->managers["CCManager"]->resetBoard();
    }

    public function updateStreet($action): void
    {
        $state = $this->game->getGameState();
        $activePlayers = $this->managers["stateManager"]->getActivePlayers($state);
        $priceToPlay = $this->managers["betManager"]->getPriceToPlay($state);

        if ($activePlayers > 1 && $priceToPlay === 0) {
            $this->managers["streetManager"]->setNextStreet();
            $this->managers["betManager"]->resetPlayerActions($state["players"]);
            $this->everyoneMoved = false;

        }
    }

    public function isShowdown(): bool
    {
        return $this->managers["streetManager"]->getShowdown();
    }



    public function everyoneMoved(): bool
    {
        return $this->managers["stateManager"]->didEveryoneMove();
    }


}