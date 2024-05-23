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
        if($state["newHand"] === true) {
            $this->managers["cardManager"]->shuffleCards();
            $this->managers["cardManager"]->dealStartHandToAllPlayers($state["players"]);
        }
    }

    public function dealCommunityCards($state)
    {
        $players = $this->game->getPlayers();
        $priceToPlay = $this->managers["betManager"]->getPriceToPlay($state);
        if ($priceToPlay === 0) {
            $street = $this->managers["streetManager"]->getStreet();
            $cardsDealt = $this->managers["CCManager"]->cardsDealt();
            $cards = $this->managers["cardManager"]->dealCommunityCards($street, $cardsDealt);
            $this->managers["CCManager"]->register($cards);
        }
    }

    public function playersActPostFlop(mixed $heroAction, array $state): void
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
            $this->opponentsInFrontMove($state);
        }
    }

    public function opponentsInFrontMove(array $state): void
    {
        //var_dump($state);

        $hero = $state["hero"];
        $heroPos = $hero->getPosition();

        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);

        foreach ($players as $player) {
            $currentPosition = $player->getPosition();
            // Player will act if still in the hand has position before hero.
            if ($player->isActive() && $currentPosition < $heroPos) {
                $priceToPlay = $this->managers["betManager"]->getPriceToPlay($this->game->getGameState());
                $potSize = $this->managers["potManager"]->getPotSize();
                $currentBiggestBet = $this->managers["betManager"]->getBiggestBet($this->game->getGameState());

                $this->managers["opponentActionManager"]->move($priceToPlay, $player, $potSize, $currentBiggestBet);
                // Return early if player closed the betting round.
                if ($this->managers["betManager"]->playerClosedAction($player, $state)) {
                    return;
                }
            }

        }
    }


    public function opponentsBehindMove(array $state): void
    {
        $hero = $state["hero"];
        $heroPos = $hero->getPosition();

        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);
        $players = $state["players"];
        foreach ($players as $player) {
            $currentPosition = $player->getPosition();
            // Player will act if still in the hand has position after hero.
            if ($player->isActive() && $currentPosition > $heroPos) {
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
        $this->managers["potManager"]->emptyPot();
        $this->managers["CCManager"]->resetBoard();
        $this->managers["cardManager"]->resetPlayerHands($players);
        $this->managers["showdownManager"]->nullShowdownWinner();
        $this->managers["streetManager"]->setShowdownFalse();
        $this->managers["streetManager"]->resetStreet();
        $this->managers["positionManager"]->updatePositions($players);
        $this->managers["potManager"]->chargeBlinds($players);

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

    public function isPreflop(): bool
    {
        return $this->managers["streetManager"]->isPreflop();
    }

    public function playersActPreflop(mixed $heroAction, array $state): void
    {
        echo"pAP";
        // if btn player is hero->quit
        // if btn player is active
        // player->move
        // else
        // play until hero turn

        if ($this->managers["stateManager"]->heroAlreadyMoved($heroAction)) {
            echo"NAmi";
            $this->heroAction($heroAction, $state["hero"]);

            // Prevent opponents from acting if hero closed the betting round.
            if ($this->managers["betManager"]->playerClosedActionPreflop($state["hero"], $state)) {
                $this->managers["streetManager"]->isPostflop();


                return;
            }
            $this->playUntilHeroTurn($state);


            // Now everyone should have made a play,
            // register that to stateManager.
            $this->managers["stateManager"]->everyoneMoved();
        }

        //this means hero has opportunity to open button
        if ($state["hero"]->getPosition() === 2) {
            return;
        }

        //
        echo "USOOP";
        $this->playUntilHeroTurn($state);
    }

    public function playUntilHeroTurn(array $state) {
        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);
        foreach ($players as $player) {
            // Player will act if still in the hand has position after hero.
            if ($player->isHero()) {
                echo"Ruffy";
            return;
            }
            echo"Zorro";

            if ($player->isActive()) {
                $priceToPlay = $this->managers["betManager"]->getPriceToPlay($this->game->getGameState());
                $potSize = $this->managers["potManager"]->getPotSize();
                $currentBiggestBet = $this->managers["betManager"]->getBiggestBet($this->game->getGameState());

                $this->managers["opponentActionManager"]->move($priceToPlay, $player, $potSize, $currentBiggestBet);
                // return early if player closed the betting round.
                if ($this->managers["betManager"]->playerClosedActionPreflop($player, $state)) {
                    if ($this->managers["streetManager"]->isPreflop()) {
                        $this->managers["streetManager"]->isPostflop();
                        // $this->managers["streetManager"]->setNextStreet();
                    }
                    return;
                }
            }
        }
    }

}
