<?php

namespace App\Poker;

class HandChecker
{
    private array $strengthArray;
    private array $rankMapping;
    private array $strengthMapping;



    public function __construct()
    {
        $this->strengthArray = [
            'Royal flush' => false,
            'Straight flush' => false,
            'Four of a kind' => false,
            'Full House' => false,
            'Flush' => false,
            'Straight' => false,
            'Three of a kind' => false,
            'Two pair' => false,
            'One pair' => false,
            'High card' => false
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

        $this->strengthMapping = [
            'Royal flush' => 0,
            'Straight flush' => 1,
            'Four of a kind' => 2,
            'Full House' => 3,
            'Flush' => 4,
            'Straight' => 5,
            'Three of a kind' => 6,
            'Two pair' => 7,
            'One pair' => 8,
            'High card' => 9
        ];
    }

    public function evaluateHand($cards)
    {
        $ranks = [];
        $suits = [];

        foreach ($cards as $card) {
            $cardValue = $card->getValue();
            var_dump($this->rankMapping[$cardValue]);
            $ranks[] = $this->rankMapping[$cardValue];
        }

        foreach ($cards as $card) {
            $cardSuit = $card->getSuit();
            $suits[] = $cardSuit;
        }

        $rankCounts = array_count_values($ranks);

        $suitsCount = array_count_values($suits);
        $maxSameSuitCount = max($suitsCount);
        $isStraight = false;

        // Check for flush
        $this->checkForFlush($maxSameSuitCount);

        // Check for straight
        $isStraight = false;
        $straight = $this->checkForStraight($ranks);
        if ($straight !== []) {
            $isStraight = true;
            $this->strengthArray['Straight'] = true;
            $upperEndCard = max($straight);
        }

        // Check for straight flush and royal flush
        $isFlush = $this->strengthArray['Flush'];
        $isStraight = $this->strengthArray['Straight'];

        if ($isStraight && $isFlush) {
            $this->strengthArray['Straight flush'] = true;
            if ($upperEndCard === 14) {
                $this->strengthArray['Royal flush'] = true;
            } 
        }

        $this->checkForQuads($rankCounts);

        // Check for full house
        $this->checkForFull($rankCounts);

        // Check for three of a kind
        $this->checkForTrips($rankCounts);

        // Check for two pair
        $this->checkForPairs($rankCounts);

        return $this->strengthArray;
    }

    public function checkForStraight(array $ranks): array
    {
        $this->strengthArray['Straight'] = false;
        sort($ranks);

        $previousRank = 0;
        $count = 0;
        $wheel = false;
        foreach ($ranks as $rank) {
            if ($rank == $previousRank) {
                continue;
            } elseif ($rank == ++$previousRank) {
                $count++;
            } else {
                if ($previousRank == 6) {
                    $wheel = true;
                }
                $count = 1;
                $previousRank = $rank;
            }

            if ($count == 5 || ($rank == 14 && $wheel)) {
                $this->strengthArray['Straight'] = true;
                //echo "Yeah a striiaght, the highest card is: " . $previousRank;
                $straight = range($previousRank - 4, $previousRank);
                return $straight;
            }
        }
        return [];
    }

    public function checkForFlush(int $maxSameSuitCount): void
    {
        $this->strengthArray['Flush'] = false;
        if ($maxSameSuitCount >= 5) {
            $this->strengthArray['Flush'] = true;
        }
    }


    public function checkForQuads(array $rankCounts): void
    {
        if (in_array(4, $rankCounts)) {
            $this->strengthArray['Four of a kind'] = true;
        }
    }

    public function checkForFull(array $rankCounts): void
    {
        if (in_array(3, $rankCounts) && in_array(2, $rankCounts)) {
            $this->strengthArray['Full House'] = true;
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
        }

        if ($pairCount > 0 && $pairCount < 2) {
            $this->strengthArray['One pair'] = true;
        }
        $this->strengthArray['High card'] = true;
    }

    public function resetStrengthArray(): void
    {
        $this->strengthArray = [
            'royalFlush' => false,
            'straightFlush' => false,
            'fourOfAKind' => false,
            'fullHouse' => false,
            'flush' => false,
            'straight' => false,
            'threeOfAKind' => false,
            'twoPair' => false,
            'onePair' => false,
            'highCard' => false
        ];
    }

    public function compareStrength($hero, $villain): object
    {
        $heroStrength = $hero->getStrength();
        $villainStrength = $villain->getStrength();

        $heroValue = 10;

        if (array_key_exists($heroStrength, $this->strengthMapping)) {
            $heroValue = $this->strengthMapping[$heroStrength];
        } 

        $villainValue = 10;

        if (array_key_exists($villainStrength, $this->strengthMapping)) {
            $villainValue = $this->strengthMapping[$villainStrength];
        } 
        var_dump($heroValue);
        var_dump($villainValue);


        $bestHand = min($heroValue, $villainValue);
        var_dump($bestHand);

        if ($bestHand === $heroValue) {
            return $hero;
        }
        return $villain;
    }

    public function getStrengthArray(): array
    {
        return $this->strengthArray;
    }
}



// Example usage:
// $hand = array(
//     array("rank" => 2, "suit" => "Hearts"),
//     array("rank" => 3, "suit" => "Hearts"),
//     array("rank" => 4, "suit" => "Hearts"),
//     array("rank" => 5, "suit" => "Hearts"),
//     array("rank" => 6, "suit" => "Hearts")
// );
