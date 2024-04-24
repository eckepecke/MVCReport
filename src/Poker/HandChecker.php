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

        // var_dump($ranks);
        // var_dump($suits);
        // echo "handovan";



        $rankCounts = array_count_values($ranks);
        $maxRank = max(array_keys($rankCounts));
        $minRank = min(array_keys($rankCounts));
        $numRanks = count($rankCounts);
        $maxFrequency = max($rankCounts);
        $maxFrequencyRank = array_search($maxFrequency, $rankCounts);



        // var_dump($rankCounts);
        // var_dump($maxRank);
        //var_dump($numRanks);
        // var_dump($maxFrequency);
        // var_dump($maxFrequencyRank);


        // var_dump($minRank);
        //var_dump($numRanks);


        //echo "value ovan";


        $suitsCount = array_count_values($suits);
        $numSuits = count($suitsCount);
        $maxSameSuitCount = max($suitsCount);
        // var_dump($suitsCount);
        // var_dump($numSuits);
        // var_dump($maxSameSuitCount);
        //$minRank = min(array_keys($rankCounts));

        //echo "suit ovan";

        $isStraight = false;
        $isFlush = false;

        // Check for flush
        if ($maxSameSuitCount >= 5) {
            $isFlush = true;
            $this->strengthArray['Flush'] = true;
            //echo "hand has flush";
        }

        // Check for straight
        $isStraight = false;
        $straight = $this->checkForStraight($ranks);
        if ($straight !== []) {
            $isStraight = true;
            $this->strengthArray['Straight'] = true;
            //echo "true";
            $upperEndCard = max($straight);
        }
        //var_dump($straight);

        // Check for straight flush and royal flush
        if ($isStraight && $isFlush) {
            if ($upperEndCard === 14) {
                $this->strengthArray['Royal flush'] = true;
                //echo "Royal Flush";
            } else {
                $this->strengthArray['Straight flush'] = true;
                //echo "Straight Flush";
            }
        }


        // Check for four of a kind
        if (in_array(4, $rankCounts)) {
            $this->strengthArray['Four of a kind'] = true;
            //echo "Four of a Kind";
        }

        // Check for full house
        if (in_array(3, $rankCounts) && in_array(2, $rankCounts)) {
            $this->strengthArray['Full House'] = true;
            //echo "Full House";
        }

        // Check for flush
        if ($isFlush) {
            $this->strengthArray['Flush'] = true;
            //echo "Flush";
        }

        // Check for straight
        if ($isStraight) {
            $this->strengthArray['Straight'] = true;
            //echo "Straight";
        }

        // Check for three of a kind
        if (in_array(3, $rankCounts)) {
            $this->strengthArray['Three of a kind'] = true;
            //echo "Three of a Kind";
        }

        // Check for two pair
        $pairCount = 0;
        foreach ($rankCounts as $count) {
            if ($count === 2) {
                $pairCount++;
            }
        }

        if ($pairCount >= 2) {
            // Two pairs exist
            $this->strengthArray['Two pair'] = true;
            //echo "Two pairs found!";
        } else {
            // Two pairs do not exist
            //echo "No two pairs found!";
        }
        if ($pairCount > 0 && $pairCount < 2) {
            $this->strengthArray['One pair'] = true;
            //echo "one pair";

        }
        $this->strengthArray['High card'] = true;
        //echo "High Card";
        //var_dump($this->strengthArray);
        return $this->strengthArray;
    }

    public function checkForStraight(array $ranks): array
    {
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
                //echo "Yeah a striiaght, the highest card is: " . $previousRank;
                $straight = range($previousRank - 4, $previousRank);
                return $straight;
            }
        }
        return [];
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
        if (array_key_exists($heroStrength, $this->strengthMapping)) {
            $heroValue = $this->strengthMapping[$heroStrength];
        } else {
            $heroValue = 10;
        }

        if (array_key_exists($villainStrength, $this->strengthMapping)) {
            $villainValue = $this->strengthMapping[$villainStrength];
        } else {
            $villainValue = 10;
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
}



// Example usage:
// $hand = array(
//     array("rank" => 2, "suit" => "Hearts"),
//     array("rank" => 3, "suit" => "Hearts"),
//     array("rank" => 4, "suit" => "Hearts"),
//     array("rank" => 5, "suit" => "Hearts"),
//     array("rank" => 6, "suit" => "Hearts")
// );
