<?php

namespace App\Poker;

/**
 * A class managing user input.
 */
class HeroActionManager
{
    /**
     * Represents the hero player's move during the game.
     *
     * @param mixed $action The action to be performed by the hero player.
     * @param Player $hero The hero player object.
     * @param int $priceToPlay The price to play for the hero player.
     * @return void
     */
    public function heroMove(mixed $action, object $hero, int $priceToPlay): void
    {
        if ($action != null && $action != "next" && $action != "observe") {
            switch ($action) {
                case "check":
                    $hero->check();
                    break;
                case "call":
                    $hero->call($priceToPlay);
                    break;
                case "fold":
                    $hero->fold();
                    break;
                default:
                    $hero->bet(intval($action));
                    break;
            }
        }
    }

    /**
     * Calculates the hero player's bet size based on the given amount and maximum bet allowed.
     *
     * @param int $amount The amount the hero player intends to bet.
     * @param int $maxBet The maximum bet allowed in the current game round.
     * @return int The calculated bet size for the hero player.
     */
    public function heroBetSize(int $amount, int $maxBet): int
    {
        return min($amount, $maxBet);
    }
}
