<?php

namespace App\Poker;

/**
 * Class SmartOpponent
 *
 * This class represents a smart opponent in a poker game. It extends the Player class and includes logic for
 * making decisions based on the strength of the hand.
 */
class SmartOpponent extends Player
{
    /**
     * SmartOpponent constructor.
     *
     * Initializes the SmartOpponent with a default stack size of 50000.
     */
    public function __construct()
    {
        parent::__construct();
        $this->stack = 50000;
    }

    /**
     * Response to a bet made by another player.
     *
     * The decision is based on the strength of the hand. If the hand strength is greater than 1, the decision
     * will be to either call or raise. If the hand strength is 1 or less, the decision can be fold, call, or raise
     * with different probabilities.
     *
     * @return string The decision made ('call', 'raise', or 'fold').
     */
    public function responseToBet()
    {
        $strength = $this->hand->getStrengthInt();
        if($strength > 1) {
            $options = [
                "call",
                "raise"
            ];
    
            $decision = $options[rand(0, 1)];
            return $decision;
        }

        $options = [
            "fold",
            "fold",
            "call",
            "call",
            "call",
            "raise"
        ];

        $decision = $options[rand(0, 5)];
        return $decision;
    }

    /**
     * Action to take when an opponent checks.
     *
     * The decision is based on the strength of the hand. If the hand strength is greater than 1, the decision
     * will be to either check or bet with different probabilities. If the hand strength is 1 or less, the decision
     * can be check or bet.
     *
     * @return string The decision made ('check' or 'bet').
     */
    public function actionVsCheck()
    {
        $strength = $this->hand->getStrengthInt();
        if($strength > 1) {
            $options = [
                "check",
                "bet",
                "bet",
                "bet",
            ];
            $decision = $options[rand(0, 3)];
            return $decision;
        }

        $options = [
            "check",
            "bet",
        ];

        $decision = $options[rand(0, 1)];
        return $decision;
    }

    /**
     * Choose the bet size based on the pot size.
     *
     * @param float $potSize The current size of the pot.
     * @return float The bet size, which is 75% of the pot size.
     */
    public function chooseBetSize($potSize): float
    {
        if ($potSize === 0) {
            return 800;
        }
        return 0.75 * $potSize;
    }

    /**
     * Action to take when an opponent shoves (goes all-in).
     *
     * The decision is based on the strength of the hand. If the hand strength is greater than 1, the decision
     * will be to call. If the hand strength is 1 or less, the decision can be fold or call with different probabilities.
     *
     * @return string The decision made ('call' or 'fold').
     */
    public function actionVsShove(): string
    {
        $strength = $this->hand->getStrengthInt();
        if($strength > 1) {
            $decision = "call";
            return $decision;
        }
        $options = [
            "fold",
            "fold",
            "fold",
            "call",
        ];

        $decision = $options[rand(0, 3)];
        return $decision;
    }
}
