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
    public function move(object $player, array $data, object $hero): void
    {
        $priceIs0 = true;
        $price = $data["price"];
        $potSize = $data["pot"];
        $bet = $data["currentBiggestBet"];

        if ($hero->isAllin()) {
            $this->oppFacingAllIn($player, $price);
            return;
        }

        if($price > 0) {
            $priceIs0 = false;
        }
        switch ($priceIs0) {
            case true:
                $this->villainActionVsNoPrice($player, $potSize);
                break;
            case false:
                $this->villainVsPrice($player, $bet, $price);
                break;
            default:
                throw new Exception("PriceIsO not true or false");
                break;
        }

    }

    public function villainVsPrice(object $player, int $bet, int $price): void
    {
        $action = $player->responseToBet();
        switch ($action) {
            case "fold":
                $player->fold();
                $player->deActivate();
                break;

            case "call":
                $player->call($price);
                break;

            default:
                $player->raise($bet);
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
                $amount = $player->chooseBetSize($potSize);

                $player->bet($amount);
                break;
            case "check":
                $player->check();
                break;
        }
    }

    public function oppFacingAllIn(object $player, int $price): void
    {
        $action = $player->actionVsShove();
        // for debugging
        switch ($action) {
            case "call":
                $player->call($price);
                break;
            case "fold":
                $player->fold();
                break;
        }
    }
}
