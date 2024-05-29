<?php

namespace App\Poker;

/**
 * Manages the game.
 */
class Manager
{
    private array $managers = [];

    public function addManager(string $key, object $manager): void
    {
        $this->managers[$key] = $manager;
    }

    public function access(string $manager): object
    {
        return $this->managers[$manager];
    }

    public function givePotToWinner(array $state): void
    {
        $this->managers["potManager"]->addChipsToPot($state);
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
    }

    public function showdown(array $state): void
    {

        $this->managers["cardManager"]->updateHandStrengths($state["players"], $state["board"]);
        $winner = $this->managers["showdownManager"]->findWinner($state["active"], $state["board"]);
        $pot = $this->managers["potManager"]->getPotSize();
        $winner->takePot($pot);
        $this->managers["streetManager"]->setShowdownTrue();

    }

    public function updatePlayersCurrentHandStrength(array $state): void
    {
        // $board = $this->managers["CCManager"]->getBoard();
        $this->managers["cardManager"]->updateHandStrengths($state["players"], $state["board"]);
    }

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

    public function opponentsBehindMove(array $state): void
    {
        $hero = $state["hero"];
        $heroPos = $hero->getPosition();
        $players = $state["players"];
        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);

        foreach ($players as $player) {
            $currentPosition = $player->getPosition();
            // Player will act if still in the hand has position after hero.
            if ($player->isActive() && $currentPosition > $heroPos) {
                $chipData = $this->getDataBeforeaction($state);
                $this->managers["opponentActionManager"]->move($player, $chipData, $hero);

                if ($this->managers["betManager"]->playerClosedAction($player, $state)) {
                    $this->managers["betManager"]->setActionIsClosed(true);
                    // unnece
                    return;
                }
            }
        }
        // Now everyone should have made a play
    }


    public function opponentsInFrontMove(array $state): void
    {
        $players = $state["players"];
        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);
        $hero = $state["hero"];
        $heroPos = $hero->getPosition();
        foreach ($players as $player) {
            $currentPosition = $player->getPosition();
            // Player will act if still in the hand has position before hero.
            if ($player->isActive() && $currentPosition < $heroPos) {
                $heroPos = $state["hero"]->getPosition();

                $chipData = $this->getDataBeforeaction($state);
                $this->managers["opponentActionManager"]->move($player, $chipData, $hero);
                // Return early if player closed the betting round.
                if ($this->managers["betManager"]->playerClosedAction($player, $state)) {
                    $this->managers["betManager"]->setActionIsClosed(true);
                    return;
                }
            }

        }
    }

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
