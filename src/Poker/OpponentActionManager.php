<?php

namespace App\Poker;

/**
 * Class OpponentManager
 * 
 * Manages player action logic in a poker game.
 */
class OpponentActionManager
{
    public function move(int $price, object $player): void
    {
        if($price > 0) {
            $action = $player->responseToBet();
            $action = "fold";
                switch ($action) {
                    case "fold":
                        $player->fold();
                        $player->deActivate();
                        break;
                    case "call":
                        $player->call($price);
                        break;
                    default:
                        $player->raise($price, $player);
                        break;
                    }
            // villain playvs check
        return;
        }
        $action = $player->actionVsCheck();
        // for debugging
        $action = 'check';
        $player->$action();

    }
}