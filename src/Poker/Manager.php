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

    public function playersAct(mixed $heroAction, array $state): void
    {
        // Either Hero has => Let everyone behind him move.
        // When he did not already move => Let everyone in front move
        // until hero' turn.
        if ($this->managers["stateManager"]->heroAlreadyMoved($heroAction)) {
            $this->heroAction($heroAction, $state["hero"]);
            echo "NaMIII";

            // Prevent opponents from acting if hero closed the betting round.
            if ($this->managers["betManager"]->playerClosedAction($state["hero"], $state)) {
                echo "SANJIII";
                return;
            }
            echo "FOXYYY";

            $this->opponentsBehindMove($state);
            // Now everyone should have made a play,
            // register that to stateManager.
            $this->managers["stateManager"]->everyoneMoved();
        } else {
            $this->opponentsInFrontMove($state);
        }
    }

    public function opponentsInFrontMove(array $state)
    {    
        $hero = $state["hero"];
        $heroPos = $hero->getPosition();

        $players = $state["players"];
        $players = $this->managers["positionManager"]->sortPlayersByPosition($players);

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
        echo "playersbehind()";
        $hero = $state["hero"];
        $heroPos = $hero->getPosition();

        // $this->positionManager->sortPlayersByPosition($players);
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

    public function handWonWithoutShowdown(): bool
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

    public function showdown(array $players): void
    {
        $this->managers["cardManager"]->updateHandStrengths($players);
        $this->managers["showdownManager"]->chipsToWinner($players);
        $this->gameProperties['challenge']->setHandWinner($winner->getName());
        $this->gameProperties['table']->setStreet(4);
        $this->showdown = true;
        $this->gameProperties['challenge']->incrementHandsPlayed();
    }

    public function updatePlayersCurrentHandStrength(array $players): void
    {
        $board = $this->managers["CCManager"]->getBoard();
        $strength = $this->managers["cardManager"]->assignStrength($players, $board);
    }


}