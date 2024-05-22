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

    // private object $showdownManager;




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

    // public function addShowdownManager(ShowdownManager $manager): void
    // {
    //     $this->showdownManager = $manager;
    // }

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
        $players = $this->game->getPlayers();
        // $activePlayers = $this->stateManager->removeInactive($state);

        $priceToPlay = $this->betManager->getPriceToPlay($state);
        if ($priceToPlay === 0) {
            $street = $this->streetManager->getStreet();
            $cardsDealt = $this->CCManager->cardsDealt();
            $cards = $this->cardManager->dealCommunityCards($street, $cardsDealt);
            $this->CCManager->register($cards);
        }
    }

    public function playersMoveTest(mixed $heroAction, array $state): void
    {
        // Maybe someting like:
        // I have to play on player per loop?
        // and have next move button in template?

        $players = $state["players"];
        if ($heroAction != null && $heroAction != "next") {
            
            $players = $this->positionManager->sortPlayersByPosition($players);
            foreach ($players as $player) {
                if ($player->isHero()) {
                    continue;
                }
                if ($player->isActive() && $heroAction != "call") {
                    $priceToPlay = $this->betManager->getPriceToPlay($this->game->getGameState());
                    $potSize = $this->potManager->getPotSize();
                    $currentBiggestBet = $this->betManager->getBiggestBet($this->game->getGameState());

    
                    $this->opponentActionManager->move($priceToPlay, $player, $potSize, $currentBiggestBet);
                }

            }
        }
        
    }

    public function playersAct(mixed $heroAction, array $state): void
    {
        // either Hero has moved
        if ($this->heroAlreadyMoved($heroAction)) {
            $this->heroAction($heroAction, $state["hero"]);
            // Prevent opponents from acting if hero closed the betting round.
            if ($this->betManager->playerClosedAction($state["hero"], $state)) {
                return;
            }
            $this->opponentsBehindMove($state);
            $this->stateManager->everyoneMoved();
        // Now everyone should have made a play
        } else {
            $this->playUntilHeroTurn($state);
        }
    //     $players = $state["players"];
    //         $players = $this->positionManager->sortPlayersByPosition($players);
    //         foreach ($players as $player) {
    //             if ($player->isHero() && $player->isActive()) {
    //                 if ($this->heroAlreadyMoved($heroAction)) {
    //                     echo "HeroMoved Already";
    //                     continue;
    //                 } else {
    //                     echo "HeroToAct";
    //                     return;
    //                 }

    //             }
    //             if ($player->isActive()) {
    //                 $priceToPlay = $this->betManager->getPriceToPlay($this->game->getGameState());
    //                 $potSize = $this->potManager->getPotSize();
    //                 $currentBiggestBet = $this->betManager->getBiggestBet($this->game->getGameState());

    //                 $this->opponentActionManager->move($priceToPlay, $player, $potSize, $currentBiggestBet);
    //             }
    //         }
    }

    public function playUntilHeroTurn(array $state)
    {    
        echo "playUntilHeroTurn()";
        $players = $state["players"];
            $players = $this->positionManager->sortPlayersByPosition($players);
            foreach ($players as $player) {
                if ($player->isHero() && $player->isActive()) {
                    echo "HeroToAct";
                    return;
                    }

                }
                if ($player->isActive()) {
                    $priceToPlay = $this->betManager->getPriceToPlay($this->game->getGameState());
                    $potSize = $this->potManager->getPotSize();
                    $currentBiggestBet = $this->betManager->getBiggestBet($this->game->getGameState());

                    $this->opponentActionManager->move($priceToPlay, $player, $potSize, $currentBiggestBet);
                    // return early if player closed the betting round.
                    if ($this->betManager->playerClosedAction($player, $state)) {
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
                $priceToPlay = $this->betManager->getPriceToPlay($this->game->getGameState());
                $potSize = $this->potManager->getPotSize();
                $currentBiggestBet = $this->betManager->getBiggestBet($this->game->getGameState());

                $this->opponentActionManager->move($priceToPlay, $player, $potSize, $currentBiggestBet);
                // return early if player closed the betting round.
                if ($this->betManager->playerClosedAction($player, $state)) {
                    return;
                }
            }
        }
        // Now everyone should have made a play
    }

    public function heroAction(mixed $action, object $player): void
    {
        // $priceToPlay = $this->betManager->getPriceToPlay($this->game->getGameState());
        $currentBiggestBet = $this->betManager->getBiggestBet($this->game->getGameState());


        $this->heroActionManager->heroMove($action, $player, $currentBiggestBet);
    }

    public function handleChips(): void
    {
        $state = $this->game->getGameState();
        $players = $state["players"];
        $priceToPlay = $this->betManager->getPriceToPlay($state);
        $activePlayers = $this->stateManager->getActivePlayers($state);

        // Adding chips to pot when there is no uncalled bet
        // or if there is only one active player (rest folded).
        if ($priceToPlay === 0 || $activePlayers < 2) {
            $this->potManager->addChipsToPot($state);
            $this->betManager->resetPlayerBets($players);
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

        if ($activePlayers > 1 && $priceToPlay === 0) {
            $this->streetManager->setNextStreet();
            $this->betManager->resetPlayerActions($state["players"]);
            $this->everyoneMoved = false;

        }
    }

    public function isShowdown(): bool
    {
        return $this->streetManager->getShowdown();
    }

    public function heroAlreadyMoved($heroAction): bool 
    {
        $heroAlreadyMoved = false;
        if ($heroAction != "next" && $heroAction != null) {
            $heroAlreadyMoved = true;
        }

        return $heroAlreadyMoved;
    }

    public function everyoneMoved(): bool
    {
        return $this->stateManager->didEveryoneMove();
    }


}