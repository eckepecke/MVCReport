<?php

namespace App\Poker;
/**
 * Class HandEvaluator
 *
 * Evaluates the strength of poker hands.
 */
class HandEvaluator
{
    /** @var array Mapping of hand ranks to their corresponding strength values. */
    private array $rankMapping;

    /** @var array The strength array containing the strength of each hand. */
    private array $strengthArray;

    /** @var array Mapping of hand strengths to their corresponding string representations. */
    private array $strengthMapping;

    public function __construct()
    {

        $this->rankMapping = [
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
            '10' => 10,
            'jack' => 11,
            'queen' => 12,
            'king' => 13,
            'ace' => 14
        ];

        $this->strengthArray = [
            'Royal flush' => false,
            'Straight flush' => false,
            'Four of a kind' => false,
            'Full house' => false,
            'Flush' => false,
            'Straight' => false,
            'Three of a kind' => false,
            'Two pair' => false,
            'One pair' => false,
            'High card' => true
        ];

        $this->strengthMapping = [
            'Royal flush' => 10,
            'Straight flush' => 9,
            'Four of a kind' => 8,
            'Full house' => 7,
            'Flush' => 6,
            'Straight' => 5,
            'Three of a kind' => 4,
            'Two pair' => 3,
            'One pair' => 2,
            'High card' => 1
        ];
    }

    /**
     * Evaluates the strength of a poker hand.
     *
     * @param array $cards An array of CardGraphic objects representing the hand to be evaluated.
     * @return array An array containing the strength of the evaluated hand.
     */
    public function evaluateHand(array $cards): array
    {
        list($ranks, $suits) = $this->extractRanksAndSuits($cards);

        $rankCounts = array_count_values($ranks);
        $suitsCount = array_count_values($suits);
        $maxSameSuitCount = max($suitsCount);

        $this->resetStrengthArray();
        $this->checkForFlush($maxSameSuitCount);
        $this->checkForStraight($ranks);
        // $this->checkForStraightFlush();
        $this->checkForQuads($rankCounts);
        $this->checkForFullHouse($rankCounts);
        $this->checkForTrips($rankCounts);
        $this->checkForPairs($rankCounts);

        return $this->getStrengthArray();
    }

    /**
     * Extracts ranks and suits from an array of cards.
     *
     * @param CardGraphic[] $cards An array of CardGraphic objects representing the cards.
     * @return array An array containing two arrays: one for ranks and one for suits.
     */
    public function extractRanksAndSuits(array $cards): array
    {
        $ranks = [];
        $suits = [];

        foreach ($cards as $card) {
            $ranks[] = $this->rankMapping[$card->getValue()];
            $suits[] = $card->getSuit();
        }

        return [$ranks, $suits];
    }

    /**
     * Checks if the hand contains a straight.
     *
     * @param array $ranks An array of integers representing the ranks of the cards.
     * @return void
     */
    public function checkForStraight(array $ranks): void
    {
        $this->strengthArray['Straight'] = false;

        if (in_array(14, $ranks)) {
            $ranks[] = 1; // Add Ace with rank 1
        }

        sort($ranks);

        $set = array($ranks[0]); // Start with the first card
        $lastRank = $ranks[0];
        foreach ($ranks as $card) {
            if ($card === $lastRank) {
                continue;
            } // Skip duplicates
            if ($card - $lastRank === 1) {
                // Consecutive card, add to the set
                $set[] = $card;
            } else {
                // Not consecutive, restart the set with the current card
                $set = array($card);
            }

            if (count($set) === 5) {
                break; // Found a straight, no need to continue
            }
            $lastRank = $card;
        }

        if (count($set) === 5) {
            //echo "Found a straight with " . implode(',', $set) . "\n";
            $this->strengthArray['Straight'] = true;
        }
    }

    // public function checkForStraightFlush(): void
    // {
    //     if (($this->strengthArray['Flush'] === true) && ($this->strengthArray['Straight'] === true)) {
    //         $this->strengthArray['Straight flush'] = true;
    //     }
    // }

    /**
     * Checks if the hand contains a flush.
     *
     * @param int $maxSameSuitCount The maximum count of cards with the same suit.
     * @return void
     */
    public function checkForFlush(int $maxSameSuitCount): void
    {

        if ($maxSameSuitCount >= 5) {
            $this->strengthArray['Flush'] = true;
        }
    }

    /**
     * Checks if the hand contains four of a kind.
     *
     * @param array $rankCounts An array containing the count of each rank in the hand.
     * @return void
     */
    public function checkForQuads(array $rankCounts): void
    {
        if (in_array(4, $rankCounts) || in_array(5, $rankCounts)) {
            $this->strengthArray['Four of a kind'] = true;
        }
    }

    /**
     * Checks if the hand contains a full house.
     *
     * @param array $rankCounts An array containing the count of each rank in the hand.
     * @return void
     */
    public function checkForFullHouse(array $rankCounts): void
    {
        if (in_array(3, $rankCounts) && in_array(2, $rankCounts)) {
            $this->strengthArray['Full house'] = true;
        }
    }

    /**
     * Checks if the hand contains three of a kind.
     *
     * @param array $rankCounts An array containing the count of each rank in the hand.
     * @return void
     */
    public function checkForTrips(array $rankCounts): void
    {
        if (in_array(3, $rankCounts)) {
            $this->strengthArray['Three of a kind'] = true;
        }
    }

    /**
     * Checks if the hand contains pairs.
     *
     * @param array $rankCounts An array containing the count of each rank in the hand.
     * @return void
     */
    public function checkForPairs(array $rankCounts): void
    {
        $pairCount = 0;
        foreach ($rankCounts as $count) {
            if ($count === 2) {
                $pairCount++;
            }
        }

        if ($pairCount >= 2) {
            $this->strengthArray['Two pair'] = true;
            return;
        }

        if ($pairCount === 1) {
            $this->strengthArray['One pair'] = true;
        }
    }

    /**
     * Resets the strength array to its initial state.
     *
     * @return void
     */
    public function resetStrengthArray(): void
    {
        $this->strengthArray = [
            'Royal flush' => false,
            'Straight flush' => false,
            'Four of a kind' => false,
            'Full house' => false,
            'Flush' => false,
            'Straight' => false,
            'Three of a kind' => false,
            'Two pair' => false,
            'One pair' => false,
            'High card' => true
        ];
    }

    /**
     * Gets the integer representation of a hand strength.
     *
     * @param string $strength The string representation of the hand strength.
     * @return int The integer representation of the hand strength.
     */
    public function getStrengthAsInt(string $strength): int
    {
        $valueAsInt = $this->strengthMapping[$strength] ?? 1;

        return $valueAsInt;
    }

    /**
     * Gets the strength array representing the evaluated hand.
     *
     * @return array The strength array representing the evaluated hand.
     */
    public function getStrengthArray(): array
    {
        return $this->strengthArray;
    }

    /**
     * Gets the current strength of the evaluated hand.
     *
     * @return string The current strength of the evaluated hand.
     */
    public function getCurrentStrength(): string
    {
        foreach ($this->strengthArray as $key => $value) {
            if ($value === true) {
                return $key;
            }
        }

        return 'No strength found';
    }
}
