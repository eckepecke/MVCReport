<?php

namespace App\Poker;

/**
 * A class managing user input.
 */
class HeroActionManager
{
    /**
     * Handles user input.
     */
    public function heroMove(mixed $action, object $hero, int $priceToPlay): void
    {
        echo "RRUUUUFFFFYYY";

        if ($action != null && $action != "next") {
            echo "heroMove()";
            var_dump($action);
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



    public function heroBetSize(int $amount, int $maxBet): int
    {
        return min($amount, $maxBet);
    }
}
