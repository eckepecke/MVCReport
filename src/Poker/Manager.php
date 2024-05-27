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

    public function givePotToWinner(array $state): void
    {
        $pot = $this->managers["potManager"]->addChipsToPot($state);
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
        $this->managers["potManager"]->chargeBlinds($players);
        $this->managers["betManager"]->resetAllIns($players);

    }

    public function showdown(array $players): void
    {
        $board = $this->managers["CCManager"]->getBoard();
        $this->managers["cardManager"]->updateHandStrengths($players, $board);
        $activePlayers = $this->managers["stateManager"]->removeInactive($players);

        $winner = $this->managers["showdownManager"]->findWinner($activePlayers, $board);
        $pot = $this->managers["potManager"]->getPotSize();
        $winner->takePot($pot);

        $this->managers["streetManager"]->setShowdownTrue();
        ///increment hands
    }

    public function updatePlayersCurrentHandStrength(array $players): void
    {
        $board = $this->managers["CCManager"]->getBoard();
        $this->managers["cardManager"]->updateHandStrengths($players, $board);
    }

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

    public function opponentsBehindMove(array $state): void
    {
        echo"OBM";
        $hero = $state["hero"];
        $heroPos = $hero->getPosition();
        $players = $state["players"];
        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);

        foreach ($players as $player) {
            $currentPosition = $player->getPosition();
            // Player will act if still in the hand has position after hero.
            if ($player->isActive() && $currentPosition > $heroPos) {
                var_dump($player->getName());
                $chipData = $this->getDataBeforeaction();
                $this->managers["opponentActionManager"]->move($player, $chipData, $hero);
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


    public function opponentsInFrontMove(array $state): void
    {
        echo "hello";
        $players = $state["players"];
        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);
        $hero = $state["hero"];
        $heroPos = $hero->getPosition();
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
                $this->managers["opponentActionManager"]->move($player, $chipData, $hero);
                // Return early if player closed the betting round.
                if ($this->managers["betManager"]->playerClosedAction($player, $state)) {
                    $this->managers["betManager"]->setActionIsClosed(true);
                    echo"Player closed action OIM!";
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

    public function opponentsPlay(mixed $heroAction, array $state): void
    {
        $actionIsClosed = $this->managers["betManager"]->getActionIsClosed();
        $newHand = $this->managers["stateManager"]->getNewHand();
        $heroMoved = $this->managers["stateManager"]->heroAlreadyMoved($heroAction);

        if ($state["hero"]->isAllIn()) {
            $this->opponentsBehindMove($state);
            $this->opponentsInFrontMove($state);
            return;
        }
        // If hero closed hte action we deal and let
        // opponents in front move, return since action is 
        // now back on hero.
        if ($actionIsClosed && !$newHand) {
            echo"Nami";
            $this->deal($state);
            $this->opponentsInFrontMove($state);
            return;
        }

        // If hero made a move Opponents behind move.
        if ($heroMoved && !$newHand) {
            echo"Robin";
            $this->opponentsBehindMove($state);
            $this->managers["stateManager"]->wonWithNoShowdown($state);

        }

        $actionIsClosed = $this->managers["betManager"]->getActionIsClosed();
        $newHand = $this->managers["stateManager"]->getNewHand();


        // If action now is closed we deal first
        // and let opponents infront move.
        if ($actionIsClosed && !$newHand) {
            $this->deal($state);
        }

        $this->opponentsInFrontMove($state);
        $this->managers["stateManager"]->wonWithNoShowdown($state);

        $actionIsClosed = $this->managers["betManager"]->getActionIsClosed();
        $newHand = $this->managers["stateManager"]->getNewHand();

        // If players infront closed the action
        // we deal and play until it is heros's turn to act.
        if ($actionIsClosed && !$newHand) {
            echo"Zorro";
            $this->deal($state);
            $this->opponentsInFrontMove($state);
        }
    }

    public function heroMakesAPlay(mixed $heroAction, array $state): void
    {
        $priceToPlay = $this->managers["betManager"]->getPriceToPlay($state);

        // Hero will move here if input was a move.
        $this->managers["heroActionManager"]->heroMove($heroAction, $state["hero"], $priceToPlay);
        if ($this->managers["betManager"]->playerClosedAction($state["hero"], $state)) {
            echo"Hero closed action";
            $this->managers["betManager"]->setActionIsClosed(true);
        }

        $this->managers["stateManager"]->wonWithNoShowdown($state);
    }

    public function dealToShowDown(): void
    {
        $board = $this->managers["CCManager"]->getBoard();
        $newCards = $this->managers["cardManager"]->dealRemaining($board);
        $this->managers["CCManager"]->register($newCards);
    }


}

