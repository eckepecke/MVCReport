<?php

namespace App\Poker;

/**
 * Class OpponentManager
 * 
 * Manages player action logic in a poker game.
 */
class OpponentActionManager
{
    public function move(int $price, object $player, int $potSize, int $bet): void
    {
        echo "price is";
        var_dump($price);

        if($price > 0) {
            $action = $player->responseToBet();
            // $action = "call";
                switch ($action) {
                    case "fold":
                    echo "opponent folds";

                        $player->fold();
                        $player->deActivate();
                        break;
                    case "call":
                    echo "opponent calls";

                        $player->call($bet);
                        //this should be bet not price
                        break;
                    default:
                    echo "opponent raises";

                        $player->raise($bet, $player);
                        break;
                    }
            // villain playvs check
        return;
        }
        $action = $player->actionVsCheck();
        var_dump($action);
        // for debugging
        // $action = 'check';
        switch ($action) {
            case "bet":
                $amount = $player->chooseBetSize($potSize);
                echo "opponent bets";
                $player->bet($amount);
                break;
            case "check":
                echo "opponent bets";
                $player->check();
                break;
        }

    }
}