<?php

namespace App\Poker;

/**
 * Manages the game.
 */
class Manager
{
    private array $managers = [];
    /**
     * Adds a manager to the game.
     *
     * @param string $key The key to associate with the manager.
     * @param object $manager The manager object to be added.
     * 
     * @return void
     */
    public function addManager(string $key, object $manager): void
    {
        $this->managers[$key] = $manager;
    }

    /**
     * Retrieves a manager by its key.
     *
     * @param string $manager The key of the manager to retrieve.
     * 
     * @return object The manager object associated with the provided key.
     */
    public function access(string $manager): object
    {
        return $this->managers[$manager];
    }

    /**
     * Distributes the pot to the winner of the game.
     *
     * @param array $state The current state of the game.
     * 
     * @return void
     */
    public function givePotToWinner(array $state): void
    {
        $this->managers["potManager"]->addChipsToPot($state);
        $winner = $this->managers["stateManager"]->getWinner($state);
        $pot = $this->managers["potManager"]->getPotSize();
        $winner->takePot($pot);
    }

    /**
     * Resets the game table for a new round.
     *
     * @param array $players An array containing the players participating in the game.
     * 
     * @return void
     */
    public function resetTable(array $players): void
    {
        $this->managers["potManager"]->resetPot();
        $this->managers["CCManager"]->resetBoard();
        $this->managers["cardManager"]->resetPlayerHands($players);
        $this->managers["betManager"]->resetPlayerBets($players);
        $this->managers["betManager"]->resetPlayerActions($players);
        $this->managers["betManager"]->resetPlayersAllIn($players);

        $this->managers["showdownManager"]->nullShowdownWinner();
        $this->managers["streetManager"]->setShowdownFalse();
        $this->managers["streetManager"]->resetStreet();
        $this->managers["cardManager"]->activatePlayers($players);
        $this->managers["positionManager"]->updatePositions($players);
    }

    /**
     * Initiates the showdown phase of the game.
     *
     * @param array $state The current state of the game.
     * 
     * @return void
     */
    public function showdown(array $state): void
    {
        $this->managers["cardManager"]->updateHandStrengths($state["players"], $state["board"]);
        $winner = $this->managers["showdownManager"]->findWinner($state["active"], $state["board"]);
        $pot = $this->managers["potManager"]->getPotSize();
        $winner->takePot($pot);
        $this->managers["streetManager"]->setShowdownTrue();
    }

    /**
     * Updates the current hand strength of each player based on the provided game state.
     *
     * @param array $state The current state of the game.
     * 
     * @return void
     */
    public function updatePlayersCurrentHandStrength(array $state): void
    {
        $this->managers["cardManager"]->updateHandStrengths($state["players"], $state["board"]);
    }

    /**
     * Prepares table for next street play.
     *
     * @param array $state The current state of the game.
     * 
     * @return void
     */
    public function deal(array $state): void
    {
        $this->managers["streetManager"]->setNextStreet($state);
        $this->managers["potManager"]->addChipsToPot($state);
        $this->managers["betManager"]->resetPlayerBets($state["players"]);
        $this->managers["betManager"]->resetPlayerActions($state["players"]);
        $this->managers["betManager"]->setActionIsClosed(false);
        $street = $this->managers["streetManager"]->getStreet();
        $cardsDealt = $this->managers["CCManager"]->cardsDealt();
        $cards = $this->managers["cardManager"]->dealCommunityCards($street, $cardsDealt);
        $this->managers["CCManager"]->register($cards);
    }

    /**
     * Making all opponents in front of the hero move.
     *
     * @param array $state The current state of the game.
     * 
     * @return void
     */
    public function opponentsBehindMove(array $state): void
    {
        if($this->managers["betManager"]->getActionIsClosed()) {
            return;
        }

        $hero = $state["hero"];
        $heroPos = $hero->getPosition();
        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);

        foreach ($players as $player) {
            $currentPosition = $player->getPosition();
            if ($player->isActive() && $currentPosition > $heroPos) {
                $chipData = $this->getDataBeforeaction($state);
                $this->managers["opponentActionManager"]->move($player, $chipData, $hero);

                if ($this->managers["betManager"]->playerClosedAction($player, $state)) {
                    $this->managers["betManager"]->setActionIsClosed(true);
                }
            }
        }
    }

    /**
     * Making all opponents behind of the hero move.
     *
     * @param array $state The current state of the game.
     * 
     * @return void
     */
    public function opponentsInFrontMove(array $state): void
    {
        if($this->managers["betManager"]->getActionIsClosed()) {
            return;
        }
        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);
        $hero = $state["hero"];
        $heroPos = $hero->getPosition();

        foreach ($players as $player) {
            $currentPosition = $player->getPosition();
            if ($player->isActive() && $currentPosition < $heroPos) {
                $heroPos = $state["hero"]->getPosition();
                $chipData = $this->getDataBeforeaction($state);
                $this->managers["opponentActionManager"]->move($player, $chipData, $hero);
                if ($this->managers["betManager"]->playerClosedAction($player, $state)) {
                    $this->managers["betManager"]->setActionIsClosed(true);
                }
            }
        }
    }

    /**
     * Retrieves the necessary data before a player's action in the game.
     *
     * This method gathers and returns relevant information from the current game state
     * that is needed by a player before they make their action.
     *
     * @param array $state The current state of the game.
     * 
     * @return array An array containing the necessary data for a player's action.
     */
    public function getDataBeforeAction(array $state): array
    {
        $priceToPlay = $this->managers["betManager"]->getPriceToPlay($state);
        $potSize = $this->managers["potManager"]->getPotSize();
        $currentBiggestBet = $this->managers["betManager"]->getBiggestBet($state);
        $chipData = [
            "price" => $priceToPlay,
            "pot" => $potSize,
            "currentBiggestBet" => $currentBiggestBet,
        ];

        return $chipData;
    }

    /**

     * Initiates opponents actions. Directing the flow depending on Hero's action and 
     * state of the game.
     *
     * @param mixed $heroAction The action taken by the hero player.
     * @param array $state The current state of the game.
     * 
     * @return void
     */
    public function opponentsPlay(mixed $heroAction, array $state): void
    {
        $actionIsClosed = $this->managers["betManager"]->getActionIsClosed();
        $newHand = $this->managers["stateManager"]->getNewHand();
        $heroMoved = $this->managers["stateManager"]->heroAlreadyMoved($heroAction);

        if ($state["hero"]->isAllIn() && $actionIsClosed) {
            return;
        }

        if ($state["hero"]->isAllIn()) {
            $this->opponentsBehindMove($state);
            $this->opponentsInFrontMove($state);
            return;
        }

        
        // If hero closed hte action we deal and let
        // opponents in front move, return since action is
        // now back on hero.
        if ($actionIsClosed && !$newHand) {
            $this->deal($state);
            $this->opponentsInFrontMove($state);
            return;
        }

        // If hero made a move Opponents behind move.
        if ($heroMoved && !$newHand) {
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
