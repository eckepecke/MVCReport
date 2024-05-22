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
        $priceIs0 = true;

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
                var_dump($failcrasch);
                break;
        }

    }

    public function villainVsPrice(object $player, int $potSize, int $bet): void
    {
        $action = $player->responseToBet();
        // $action = "call";
            switch ($action) {
                case "fold":
                echo "OFOLD!";
                    $player->fold();
                    $player->deActivate();
                    break;

                case "call":
                echo "OCALL!";
                    $player->call($bet);

                    break;

                default:
                echo "ORAISE!";

                    $player->raise($bet, $player);
                    break;
                }

    }

    public function villainActionVsNoPrice(object $player, int $potSize): void
    {
        $action = $player->actionVsCheck();
        var_dump($action);
        // for debugging
        // $action = 'check';
        switch ($action) {
            case "bet":
                $amount = $player->chooseBetSize($potSize);
                echo "OBET!";
                $player->bet($amount);
                break;
            case "check":
                echo "OCHECK!";
                $player->check();
                break;
        }
    }


}