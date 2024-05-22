<?php

namespace App\Poker;

/**
 * Trait TurnDirector
 * 
 * Directs the flow so that players act only when it is their turn to play.
 */
trait TurnDirector
{
    public function handleGameFlow() {
                // Either Hero has => Let everyone behind him move.
        // When he did not already move => Let everyone in front move
        // until hero' turn.
        if ($this->heroAlreadyMoved($heroAction)) {
            $this->heroAction($heroAction, $state["hero"]);
            // Prevent opponents from acting if hero closed the betting round.
            if ($this->betManager->playerClosedAction($state["hero"], $state)) {
                return;
            }

            $this->opponentsBehindMove($state);
            // Now everyone should have made a play,
            // register that to stateManager.
            $this->stateManager->everyoneMoved();
        } else {
            $this->playUntilHeroTurn($state);
        }
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
}
