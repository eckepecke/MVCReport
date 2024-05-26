<?php

namespace App\Poker;

use Exception;

/**
 * Class OpponentManager
 *
 * Manages player action logic in a poker game.
 */
class OpponentActionManager
{
    public function move(object $player, array $data): void
    {
        $priceIs0 = true;
        echo"move() PRICE:";
        $price = $data["price"];
        $potSize = $data["pot"];
        $bet = $data["currentBiggestBet"];

        var_dump($price);

        if($price > 0) {
            $priceIs0 = false;
        }
        switch ($priceIs0) {
            case true:
                $this->villainActionVsNoPrice($player, $potSize);
                break;
            case false:
                $this->villainVsPrice($player, $potSize, $bet);
                break;
            default:
                throw new Exception("PriceIsO not true or false");
                break;
        }

    }

    public function villainVsPrice(object $player, int $potSize, int $bet): void
    {
        $action = $player->responseToBet();
        // $action = "fold";
        switch ($action) {
            case "fold":
                echo "fold";

                $player->fold();
                $player->deActivate();
                break;

            case "call":
                echo "call";
                $player->call($bet);

                break;

            default:
                echo "raise";

                $player->raise($bet, $player);
                break;
        }

    }

    public function villainActionVsNoPrice(object $player, int $potSize): void
    {
        $action = $player->actionVsCheck();
        // for debugging
        // $action = 'bet';
        switch ($action) {
            case "bet":
                echo "bet";
                $amount = $player->chooseBetSize($potSize);

                $player->bet($amount);
                break;
            case "check":
                echo "check";
                $player->check();
                break;
        }
    }


}
