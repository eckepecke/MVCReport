<?php

namespace App\Poker;

use App\Poker\CardHand;
use App\Poker\Dealer;

/**
 * Class CardManager
 *
 * Manages cards and extends the Dealer class.
 */
class CardManager extends Dealer
{
    /** @var object The evaluator object used for evaluating hands. */
    private object $evaluator;

    /**
     * Deals starting hands to players.
     *
     * @param Player[] $players An array of Player objects to deal hands to.
     * @return void
     */
    public function dealStartingHands(array $players): void
    {
        $this->shuffleCards();
        foreach ($players as $player) {
            $cards = $this->dealStartHand();
            $hand = new CardHand();
            $hand->add($cards[0]);
            $hand->add($cards[1]);
            $player->addHand($hand);
        }
    }

    /**
     * Resets the hands of players.
     *
     * @param Player[] $players An array of Player objects whose hands need to be reset.
     * @return void
     */
    public function resetPlayerHands(array $players): void
    {
        foreach ($players as $player) {
            $player->resetHand();
        }
    }

    /**
     * Activates players.
     *
     * @param Player[] $players An array of Player objects to be activated.
     * @return void
     */
    public function activatePlayers(array $players): void
    {
        foreach ($players as $player) {
            $player->activate();
        }
    }


    /**
     * Deals community cards for a specific street.
     *
     * @param string $street The street for which community cards are being dealt ("flop", "turn", or "river").
     * @param int $cardsDealt The number of community cards already dealt for the specified street.
     * @return array An array containing the community cards dealt for the specified street.
     */
    public function dealCommunityCards(string $street, int $cardsDealt): array
    {
        $cards = [];

        switch ($street) {
            case "flop":
                if ($cardsDealt < 1) {
                    $cards = $this->dealFlop();
                }
                break;
            case "turn":
                if ($cardsDealt < 4) {
                    $cards = $this->dealOne();
                }
                break;
            case "river":
                if ($cardsDealt < 5) {
                    $cards = $this->dealOne();
                }
                break;
        }
        return $cards;
    }

    /**
     * Adds a HandEvaluator object to the CardManager.
     *
     * @param HandEvaluator $evaluator The HandEvaluator object to be added.
     * @return void
     */
    public function addEvaluator(HandEvaluator $evaluator): void
    {
        $this->evaluator = $evaluator;
    }

    /**
     * Updates the strengths of hands for each player based on the current board.
     *
     * @param Player[] $players An array of Player objects.
     * @param CardGraphic[] $board An array of CardGraphic objects representing the community cards on the board.
     * @return void
     */
    public function updateHandStrengths(array $players, array $board): void
    {
        foreach ($players as $player) {
            $hand = $player->getHand();
            $cardsInHand = $hand->getCardArray();
            $fullHand = $this->fuseHandAndBoard($cardsInHand, $board);
            // $this->addBoardToHand($player, $fullHand);

            $strengthArray = $this->evaluator->evaluateHand($fullHand);
            $strength = $this->evaluator->getCurrentStrength($strengthArray);
            $hand->setStrengthString($strength);
            $strengthAsInt = $this->evaluator->getStrengthAsInt($strength);
            $hand->setStrengthInt($strengthAsInt);
        }
    }

    /**
     * Fuses the hole cards and board cards into a single array representing the full hand.
     *
     * @param CardGraphic[] $holeCards An array of CardGraphic objects representing the hole cards.
     * @param CardGraphic[] $board An array of CardGraphic objects representing the community cards on the board.
     * @return CardGraphic[] An array containing all cards in the hand (hole cards + board cards).
     */
    public function fuseHandAndBoard(array $holeCards, array $board): array
    {
        return array_merge($holeCards, $board);
    }

    /**
     * Deals remaining community cards to complete the board.
     *
     * @param CardGraphic[] $board An array of CardGraphic objects representing the current community cards on the board.
     * @return CardGraphic[] An array containing the remaining community cards to complete the board.
     */
    public function dealRemaining(array $board): array
    {
        $remaining = 5 - count($board);
        $cards = $this->deck->drawMany($remaining);
        return $cards;
    }
}
