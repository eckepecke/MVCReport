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
        echo"PlayersACtPostflop";
        // Either Hero has => Let everyone behind him move.
        // When he did not already move => Let everyone in front move
        // until hero' turn.
        if ($this->managers["stateManager"]->heroAlreadyMoved($heroAction)) {
            echo"hero moved";
            var_dump($heroAction);

            $this->heroAction($heroAction, $state["hero"]);

            // Prevent opponents from acting if hero closed the betting round.
            if ($this->managers["betManager"]->playerClosedAction($state["hero"], $state)) {
                $this->managers["stateManager"]->everyoneMoved();
                
                echo"hero closed action";
                // $this->managers["stateManager"]->everyoneMoved();
                // $heroPos = $state["hero"]
                // if ($this->managers["positionManager"]->playerIsLast($state["hero"])) {
                //     $heroAction = null;
                //     echo"heroLasTPLAytAgain";

                    
                //     $this->playersActPostFlop($heroAction);
                // }
                return;
            }

            $this->opponentsBehindMove($state);
            // Now everyone should have made a play,
            // register that to stateManager.
            // $this->managers["stateManager"]->everyoneMoved();
        } else {
            $this->opponentsInFrontMove($state);

        }

    }

    public function opponentsInFrontMove(array $state): void
    {
        //var_dump($state);
        echo"OIM";


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
                    echo"Player closed action OIM!";
                    // $this->managers["stateManager"]->everyoneMoved();
                    $this->updatePhase();
                    return;
                }
            }

        }
    }


    public function opponentsBehindMove(array $state): void
    {
        echo"OBM";
    
        $hero = $state["hero"];
        $heroPos = $hero->getPosition();

        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);
        $players = $state["players"];
        foreach ($players as $player) {
            $currentPosition = $player->getPosition();
            // Player will act if still in the hand has position after hero.
            if ($player->isActive() && $currentPosition > $heroPos) {
                var_dump($player->getName());
                $priceToPlay = $this->managers["betManager"]->getPriceToPlay($this->game->getGameState());
                $potSize = $this->managers["potManager"]->getPotSize();
                $currentBiggestBet = $this->managers["betManager"]->getBiggestBet($this->game->getGameState());

                $this->managers["opponentActionManager"]->move($priceToPlay, $player, $potSize, $currentBiggestBet);
                // return early if player closed the betting round.
                if ($this->managers["betManager"]->playerClosedAction($player, $state)) {
                    echo"Player closed action OBM!";
                    // $this->managers["stateManager"]->everyoneMoved();
                    $this->updatePhase();
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

    public function handleChips(array $state): void
    //this also dont work preflop
    {
        echo"HANDLING CHIPS";
        $players = $state["players"];
        $priceToPlay = $this->managers["betManager"]->getPriceToPlay($state);
        $activePlayers = $this->managers["stateManager"]->getActivePlayers($state);

        // Adding chips to pot when there is no uncalled bet
        // or if there is only one active player (rest folded).
        if ($priceToPlay === 0 || $activePlayers < 2) {
            echo"Triggerd";

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
        $this->managers["potManager"]->resetPot();
        $this->managers["CCManager"]->resetBoard();
        $this->managers["cardManager"]->resetPlayerHands($players);
        $this->managers["showdownManager"]->nullShowdownWinner();
        $this->managers["streetManager"]->setShowdownFalse();
        $this->managers["streetManager"]->resetStreet();
        $this->managers["positionManager"]->updatePositions($players);
        // $this->managers["potManager"]->chargeBlinds($players);

    }


    public function updateStreet($state): void
    {
        $activePlayers = $this->managers["stateManager"]->getActivePlayers($state);
        $priceToPlay = $this->managers["betManager"]->getPriceToPlay($state);
/// This doesnt work prefop
        if ($activePlayers > 1 && $priceToPlay === 0) {
            $this->managers["streetManager"]->setNextStreet();
            $this->managers["betManager"]->resetPlayerActions($state["players"]);
            $this->managers["stateManager"]->everyoneHasNotMoved();

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
        $this->managers["preflopManager"]->buttoActFirst($state["players"]);

        if ($this->managers["stateManager"]->heroAlreadyMoved($heroAction)) {

            $this->heroAction($heroAction, $state["hero"]);

            // Prevent opponents from acting if hero closed the betting round.
            if ($this->managers["betManager"]->playerClosedAction($state["hero"], $state)) {
                $this->managers["streetManager"]->isPostflop();
                return;
            }
            
            //When Hero moved on the button, players in blinds
            // are next to act.
            if ($state["hero"]->getPosition() === 2) {
                $this->opponentsInFrontMove($state);
                return;
            } 
            // I an all other cases Players behind Hero 
            // should now move.
            $this->opponentsBehindMove($state);
        }

        // Hero did not move but is first to act on the button.
        // break the flow
        if ($state["hero"]->getPosition() === 2) {
            return;
        }

        // Hero did not move, opponents in front should move.
        echo "USOOP";
        if (!$btnHasActed) {
            echo "CHOPPY";

            $this->opponentsBehindMove($state);
        }
        $this->opponentsInfrontMove($state);
    }


    public function updatePhase(): void
    {
        if ($this->managers["streetManager"]->isPreflop()) {
            $this->managers["streetManager"]->isPostflop();
        }
    }

    public function TESTplayersActPostFlop(mixed $heroAction, array $state): void
    {


        // Check if hero has acted
        var_dump($heroAction);
        if (!$this->managers["stateManager"]->heroAlreadyMoved($heroAction)) {
            echo"Hero has not moved ";
            //play until his turn
            $this->playUntilHero($state);
            //added this return for Btn action
            return;

        }


        $this->heroAction($heroAction, $state["hero"]);
        //Prevent opponents from acting if hero closed the betting round.
        if ($this->managers["betManager"]->playerClosedAction($state["hero"], $state)) {
            $inPositionClose = $this->managers["positionManager"]->playerIsLast($state["hero"], $state["players"]);
            switch ($inPositionClose) {
                case true:
                    echo"hero closed IP";
                    // deal next street if he closed IP
                    // now opponents will act
                    $this->heroClosedInPosition($state);

                    break;
                case false:
                    $this->managers["stateManager"]->everyoneMoved();
                    echo"hero closed action";
                    break;
            return;
            }

        }

        $this->opponentsBehindMove($state);

        if ($this->managers["positionManager"]->playerIsLast($state["hero"], $state["players"])) {
            $this->playUntilHero($state);
        }

            // Now everyone should have made a play,
            // register that to stateManager.
            // $this->managers["stateManager"]->everyoneMoved();

    }


    public function playUntilHero(array $state): void
    {
        echo"PlayUntilHero";
        $hero = $state["hero"];
        $heroPos = $hero->getPosition();

        $players = $this->managers["positionManager"]->sortPlayersByPosition($state["players"]);

        foreach ($players as $player) {
            if ($player->isHero()) {
                return;
            }

            // Player will act if still in the hand has position before hero.
            if ($player->isActive()) {
                $chipData = $this->getDataBeforeaction();
                $this->managers["opponentActionManager"]->move($player, $chipData);
                if ($this->managers["betManager"]->playerClosedAction($player, $state)) {
                    $this->managers["betManager"]->setActionIsClosed(true);

                    // $this->managers["stateManager"]->everyoneMoved();
                }
            }

        }
    }

    public function heroClosedInPosition(array $state): void
    {
        // When Hero closes IP I need to play again
        // for a nice flow.
        $this->updateStreet($state);
        $this->dealCommunityCards($state);
        $this->playUntilHero($state);
    }



    public function preflopRevised(mixed $heroAction, array $state): void
    {
        echo"preflopRevised()";

        $priceToPlay = $this->managers["betManager"]->getPriceToPlay($state);
        $this->managers["heroActionManager"]->heroMove($heroAction, $state["hero"], $priceToPlay);
        if ($this->managers["betManager"]->playerClosedActionPreflop($state["hero"], $state)) {
            $this->managers["bettManager"]->setActionIsClosed(true);
        }

        $actionIsClosed = $this->managers["betManager"]->getActionIsClosed();
        if ($actionIsClosed) {
            $this->deal($state);
            return;
        }

        $this->opponentsPlayPreflop($heroAction, $state);
    }

    public function deal(array $state): void
    {
        echo"deal()";
        $this->managers["streetManager"]->setNextStreet($state);
        $this->managers["potManager"]->addChipsToPot($state);
        $this->managers["betManager"]->resetPlayerBets($state["players"]);
        $this->managers["betManager"]->setActionIsClosed(false);
        echo"OpenActionAgain";
        $street = $this->managers["streetManager"]->getStreet();
        $cardsDealt = $this->managers["CCManager"]->cardsDealt();
        $cards = $this->managers["cardManager"]->dealCommunityCards($street, $cardsDealt);
        $this->managers["CCManager"]->register($cards);
    }

    public function opponentsPlayPreflop(mixed $heroAction, array $state): void
    {
        echo"opponentsPlayPreflop()";
        $heroMoved = $this->managers["stateManager"]->heroAlreadyMoved($heroAction);
        $hero = $state["hero"];
        $heroPos = $hero->getPosition();
        switch ($heroPos) {
            case 2:

                $this->OIM($heroPos, $state);
                break;
            case 0:
                $this->buttonPlayerMove($state);
                if ($heroMoved) {
                    $this->OBM($heroPos, $state);
                }
            case 1:
                
                $this->buttonPlayerMove($state);
                $this->playUntilHero($state);
                if ($heroMoved) {
                    $this->OBM($heroPos, $state);
                }
                break;
        }
    }


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

    public function buttonPlayerMove($state): void
    {
        $btnPlayer = $this->managers["positionManager"]->findButtonPlayer($state["players"]);
        if ($btnPlayer->isActive() && $btnPlayer->getCurrentBet() === 0) {
            $chipData = $this->getDataBeforeaction();
            $this->managers["opponentActionManager"]->move($btnPlayer, $chipData);
        }
    }
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


        // If hero closed hte action we deal and let
        // opponents in front move, return since action is 
        // now back on hero.
        if ($actionIsClosed) {
            echo"Nami";
            $this->deal($state);
            $this->OIM($state);
            return;
        }



        if ($heroMoved) {
            echo"Robin";

            $this->OBM($state);
        }

        $actionIsClosed = $this->managers["betManager"]->getActionIsClosed();

        switch ($actionIsClosed) {
                case true:
                    echo"Sankji";

                    $this->deal($state);
                    $this->OIM($state);
                    break;
                case false:
                    echo"Chopper";

                    $this->OIM($state);
                    break;
            }

        $actionIsClosed = $this->managers["betManager"]->getActionIsClosed();

        if ($actionIsClosed) {
            echo"Zorro";

            $this->deal($state);
            $this->OIM($state);
        }
    }

    public function handleHeroInPositionClose(array $state): void
    {
        $inPositionClose = $this->managers["positionManager"]->playerIsLast($state["hero"], $state["players"]);
        switch ($inPositionClose) {
            case true:
                echo"hero closed IP";
                // deal next street if he closed IP
                // now opponents will act
                $this->deal();
                $this->OIM($state);

                break;
            case false:
                $this->managers["stateManager"]->everyoneMoved();
                echo"hero closed action";
                break;
        return;
    }
}

}

