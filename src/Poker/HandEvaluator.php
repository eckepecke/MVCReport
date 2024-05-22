<?php

namespace App\Poker;

class HandEvaluator
{
    private array $handRanks;
    private array $rankMapping;
    private array $strengthArray;
    private array $strengthMapping;

    public function __construct()
    {
        $this->handRanks = [
            'Royal flush' => false,
            'Straight flush' => false,
            'Four of a kind' => false,
            'Full House' => false,
            'flush' => false,
            'Straight' => false,
            'Three of a kind' => false,
            'Two pair' => false,
            'One pair' => false,
            'High card' => true,
        ];

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
            'Royal flush' => 0,
            'Straight flush' => 1,
            'Four of a kind' => 2,
            'Full house' => 3,
            'Flush' => 4,
            'Straight' => 5,
            'Three of a kind' => 6,
            'Two pair' => 7,
            'One pair' => 8,
            'High card' => 9
        ];
    }

    public function evaluateHand(array $cards): array
    {
        list($ranks, $suits) = $this->extractRanksAndSuits($cards);

        $rankCounts = array_count_values($ranks);
        $suitsCount = array_count_values($suits);
        $maxSameSuitCount = max($suitsCount);

        $this->resetStrengthArray();
        $this->checkForFlush($maxSameSuitCount);
        $this->checkForStraight($ranks);
        $this->checkForStraightFlush();
        $this->checkForQuads($rankCounts);
        $this->checkForFullHouse($rankCounts);
        $this->checkForTrips($rankCounts);
        $this->checkForPairs($rankCounts);

        return $this->getStrengthArray();
    }

    private function extractRanksAndSuits(array $cards): array
    {
        $ranks = [];
        $suits = [];

        foreach ($cards as $card) {
            $ranks[] = $this->rankMapping[$card->getValue()];
            $suits[] = $card->getSuit();
        }

        return [$ranks, $suits];
    }

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

    public function checkForStraightFlush(): void
    {
        if (($this->strengthArray['Flush'] === true) && ($this->strengthArray['Straight'] === true)) {
            $this->strengthArray['Straight flush'] = true;
        }
    }

    public function checkForFlush(int $maxSameSuitCount): void
    {

        if ($maxSameSuitCount >= 5) {
            $this->strengthArray['Flush'] = true;
        }
    }

    public function checkForQuads(array $rankCounts): void
    {
        if (in_array(4, $rankCounts) || in_array(5, $rankCounts)) {
            $this->strengthArray['Four of a kind'] = true;
        }
    }

    public function checkForFullHouse(array $rankCounts): void
    {
        if (in_array(3, $rankCounts) && in_array(2, $rankCounts)) {
            $this->strengthArray['Full house'] = true;
        }
    }

    public function checkForTrips(array $rankCounts): void
    {
        if (in_array(3, $rankCounts)) {
            $this->strengthArray['Three of a kind'] = true;
        }
    }

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

    public function compareStrength($hero, $villain): object
    {
        $heroStrength = $hero->getStrength();
        $villainStrength = $villain->getStrength();

        $heroValue = $this->strengthMapping[$heroStrength] ?? 10;
        $villainValue = $this->strengthMapping[$villainStrength] ?? 10;

        if ($heroValue === $villainValue) {
            return $villain;
        }

        return $heroValue < $villainValue ? $hero : $villain;
    }

    public function getStrengthArray(): array
    {
        return $this->strengthArray;
    }

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
