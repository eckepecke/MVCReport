<?php

namespace App\Poker;

/**
 * Manages the game.
 */
class Manager
{
    private object $game;
    private array $managers = [];

    public function addGame(Game $game): void
    {
        $this->game = $game;
    }

    public function addManager(string $key, object $manager): void
    {
        $this->managers[$key] = $manager;
    }

    public function access(string $manager): object
    {
        return $this->managers[$manager];
    }

    public function dealStartingHands($state)
    {
        $this->managers["cardManager"]->shuffleCards();
        $this->managers["cardManager"]->dealStartHandToAllPlayers($state["players"]);

    }


    public function newHandStarting(mixed $action): bool
    {
        $state = $this->game->getGameState();
        $activePlayers = $this->managers["stateManager"]->getActivePlayers($state);
        $newHand = false;
        if ($activePlayers < 2) {
            $newHand = true;
        }

        if ($this->managers["streetManager"]->getShowdown()) {
            $newHand = true;
        }

        if ($action === null) {
            $newHand = true;
        }

        return $newHand;
    }

    public function givePotToWinner(): void
    {
        $state = $this->game->getGameState();
        // broken
        //need seperate sd and non sd dealings after all
        $winner = $this->managers["stateManager"]->getWinner($state);
        $pot = $this->managers["potManager"]->getPotSize();
        $winner->takePot($pot);
    }

    public function resetTable(array $players): void
    {
        $this->managers["potManager"]->resetPot();
        $this->managers["CCManager"]->resetBoard();
        $this->managers["cardManager"]->resetPlayerHands($players);
        $this->managers["betManager"]->resetPlayerBets($players);
        $this->managers["betManager"]->resetPlayerActions($players);
        $this->managers["showdownManager"]->nullShowdownWinner();
        $this->managers["streetManager"]->setShowdownFalse();
        $this->managers["streetManager"]->resetStreet();
        $this->managers["cardManager"]->activatePlayers($players);
        $this->managers["positionManager"]->updatePositions($players);
        // $this->managers["potManager"]->chargeBlinds($players);
    }


    public function isShowdown(): bool
    {
        return $this->managers["streetManager"]->getShowdown();
    }


    public function showdown(array $players): void
    {
        $board = $this->managers["CCManager"]->getBoard();
        $this->managers["cardManager"]->updateHandStrengths($players, $board);
        $activePlayers = $this->managers["stateManager"]->removeInactive($players);

        $winner = $this->managers["showdownManager"]->findWinner($activePlayers);
        // $pot = $this->managers["potManager"]->getPotSize();
        // $winner->takePot($pot);

        $this->managers["streetManager"]->setShowdownTrue();
        ///increment hands
    }

    public function updatePlayersCurrentHandStrength(array $players): void
    {
        $board = $this->managers["CCManager"]->getBoard();
        $strength = $this->managers["cardManager"]->updateHandStrengths($players, $board);
    }

    public function getShowdownWinnerName(): object
    {
        $winner = $this->managers["showdownManager"]->getWinner();

        return $winner->getName();
    }

    // public function isPreflop(): bool
    // {
    //     return $this->managers["streetManager"]->isPreflop();
    // }


    // public function updatePhase(): void
    // {
    //     if ($this->managers["streetManager"]->isPreflop()) {
    //         $this->managers["streetManager"]->isPostflop();
    //     }
    // }


    // public function preflopRevised(mixed $heroAction, array $state): void
    // {
    //     echo"preflopRevised()";

    //     $priceToPlay = $this->managers["betManager"]->getPriceToPlay($state);
    //     $this->managers["heroActionManager"]->heroMove($heroAction, $state["hero"], $priceToPlay);
    //     if ($this->managers["betManager"]->playerClosedActionPreflop($state["hero"], $state)) {
    //         $this->managers["bettManager"]->setActionIsClosed(true);
    //     }

    //     $actionIsClosed = $this->managers["betManager"]->getActionIsClosed();
    //     if ($actionIsClosed) {
    //         $this->deal($state);
    //         return;
    //     }

    //     $this->opponentsPlayPreflop($heroAction, $state);
    // }

    public function deal(array $state): void
    {
        echo"deal()";
        $this->managers["streetManager"]->setNextStreet($state);
        $this->managers["potManager"]->addChipsToPot($state);
        $this->managers["betManager"]->resetPlayerBets($state["players"]);
        $this->managers["betManager"]->resetPlayerActions($state["players"]);

        $this->managers["betManager"]->setActionIsClosed(false);
        echo"OpenActionAgain";
        $street = $this->managers["streetManager"]->getStreet();
        $cardsDealt = $this->managers["CCManager"]->cardsDealt();
        $cards = $this->managers["cardManager"]->dealCommunityCards($street, $cardsDealt);
        $this->managers["CCManager"]->register($cards);
    }

    // public function opponentsPlayPreflop(mixed $heroAction, array $state): void
    // {
    //     echo"opponentsPlayPreflop()";
    //     $heroMoved = $this->managers["stateManager"]->heroAlreadyMoved($heroAction);
    //     $hero = $state["hero"];
    //     $heroPos = $hero->getPosition();
    //     switch ($heroPos) {
    //         case 2:

    //             $this->OIM($heroPos, $state);
    //             break;
    //         case 0:
    //             $this->buttonPlayerMove($state);
    //             if ($heroMoved) {
    //                 $this->OBM($heroPos, $state);
    //             }
    //         case 1:
                
    //             $this->buttonPlayerMove($state);
    //             $this->playUntilHero($state);
    //             if ($heroMoved) {
    //                 $this->OBM($heroPos, $state);
    //             }
    //             break;
    //     }
    // }


    public function OBM(array $state): void
    {
        echo"OBM";
        $heroPos = $state["hero"]->getPosition();
        $players = $state["players"];
        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);

        foreach ($players as $player) {
            $currentPosition = $player->getPosition();
            // Player will act if still in the hand has position after hero.
            if ($player->isActive() && $currentPosition > $heroPos) {
                var_dump($player->getName());
                $chipData = $this->getDataBeforeaction();
                $this->managers["opponentActionManager"]->move($player, $chipData);
                // return early if player closed the betting round.
                if ($this->managers["betManager"]->playerClosedAction($player, $state)) {
                    $this->managers["betManager"]->setActionIsClosed(true);

                    echo"Player closed action OBM!";
                    // $this->managers["stateManager"]->everyoneMoved();
                    // $this->updatePhase();
                    return;
                }
            }
        }
        // Now everyone should have made a play
    }


    public function OIM(array $state): void
    {
        echo "hello";
        $players = $state["players"];
        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);
        $heroPos = $state["hero"]->getPosition();
        var_dump($heroPos);
        foreach ($players as $player) {
            $currentPosition = $player->getPosition();
            var_dump($currentPosition);
            var_dump($player->IsHero());


            // Player will act if still in the hand has position before hero.
            if ($player->isActive() && $currentPosition < $heroPos) {
                var_dump($player->getName());
                $heroPos = $state["hero"]->getPosition();

                var_dump($heroPos);

                $chipData = $this->getDataBeforeaction();
                $this->managers["opponentActionManager"]->move($player, $chipData);
                // Return early if player closed the betting round.
                if ($this->managers["betManager"]->playerClosedAction($player, $state)) {
                    $this->managers["betManager"]->setActionIsClosed(true);

                    echo"Player closed action OIM!";
                    // $this->managers["stateManager"]->everyoneMoved();
                    $this->updatePhase();
                    return;
                }
            }

        }
    }

    public function getDataBeforeAction(): array
    {
        $priceToPlay = $this->managers["betManager"]->getPriceToPlay($this->game->getGameState());
        $potSize = $this->managers["potManager"]->getPotSize();
        $currentBiggestBet = $this->managers["betManager"]->getBiggestBet($this->game->getGameState());
        $chipData = [
            "price" => $priceToPlay,
            "pot" => $potSize,
            "currentBiggestBet" => $currentBiggestBet,
        ];

        return $chipData;
    }

    // public function buttonPlayerMove($state): void
    // {
    //     $btnPlayer = $this->managers["positionManager"]->findButtonPlayer($state["players"]);
    //     if ($btnPlayer->isActive() && $btnPlayer->getCurrentBet() === 0) {
    //         $chipData = $this->getDataBeforeaction();
    //         $this->managers["opponentActionManager"]->move($btnPlayer, $chipData);
    //     }
    // }
///////////////////////////////////////////////////////////////////////////////
    public function postflopRevised(mixed $heroAction, array $state): void
    {
        echo"postflopRevised()";

        $priceToPlay = $this->managers["betManager"]->getPriceToPlay($state);

        // Hero will move here if input was a move.
        $this->managers["heroActionManager"]->heroMove($heroAction, $state["hero"], $priceToPlay);
        if ($this->managers["betManager"]->playerClosedAction($state["hero"], $state)) {
            echo"Hero closed action";
            $this->managers["betManager"]->setActionIsClosed(true);
        }

        // Gather info on the state.
        $actionIsClosed = $this->managers["betManager"]->getActionIsClosed();
        $heroMoved = $this->managers["stateManager"]->heroAlreadyMoved($heroAction);
        $heroPos = $state["hero"]->getPosition();
        $this->wonWithNoShowdown($state);



        // If hero closed hte action we deal and let
        // opponents in front move, return since action is 
        // now back on hero.
        if ($actionIsClosed) {
            echo"Nami";
            $this->deal($state);
            $this->OIM($state);
            $this->wonWithNoShowdown($state);

            return;
        }


        // If hero made a move Opponents behind move.
        if ($heroMoved) {
            echo"Robin";
            $this->OBM($state);
            $this->wonWithNoShowdown($state);

        }


        $actionIsClosed = $this->managers["betManager"]->getActionIsClosed();
        $newHand = $this->managers["stateManager"]->getNewHand();


        // If action now is closed we deal first
        // and let opponents infront move.
        if ($actionIsClosed) {
            $this->deal($state);
        }

        $this->OIM($state);
        $this->wonWithNoShowdown($state);


        // switch ($actionIsClosed) {
        //         case true:
        //             echo"Sankji";

        //             $this->deal($state);
        //             $this->OIM($state);
        //             break;
        // // Otherwise 
        //         case false:
        //             echo"Chopper";
        //             $this->OIM($state);
        //             break;
        //     }

        $actionIsClosed = $this->managers["betManager"]->getActionIsClosed();
        // If players infront closed the action
        // we deal and play until it is heros's turn to act.
        if ($actionIsClosed) {
            echo"Zorro";

            $this->deal($state);
            $this->OIM($state);
        }
    }



    public function wonWithNoShowdown(array $state): void
    {
        echo"Tjena!";

        $activePlayers = $this->managers["stateManager"]->getActivePlayers($state);
        if ($activePlayers < 2) {
            echo"WIN!";
            $winner = $this->managers["stateManager"]->getWinner($state);
            $this->managers["potManager"]->addChipsToPot($state);
            $pot = $this->managers["potManager"]->getPotSize();
            $winner->takePot($pot);
            $this->managers["stateManager"]->setNewHand(true);
        }
    }
}

