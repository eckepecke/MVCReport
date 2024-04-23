<?php

namespace App\Poker;

class HandChecker
{
    public function evaluateHand($cards) {
        $rankMapping = [
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

        $ranks = [];
        $suits = [];


        foreach ($cards as $card) {
            $cardValue = $card->getValue();
            var_dump($rankMapping[$cardValue]);
            $ranks[] = $rankMapping[$cardValue];
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
        var_dump($maxFrequency);
        var_dump($maxFrequencyRank);


        // var_dump($minRank);
        //var_dump($numRanks);


        echo "value ovan";


        $suitsCount = array_count_values($suits);
        $numSuits = count($suitsCount);
        $maxSameSuitCount = max($suitsCount);
        var_dump($suitsCount);
        var_dump($numSuits);
        var_dump($maxSameSuitCount);
        //$minRank = min(array_keys($rankCounts));

        echo "suit ovan";

        $isStraight = false;
        $isFlush = false;
    
        // Check for flush
        if ($maxSameSuitCount >= 5) {
            $isFlush = true;
            echo "hand has flush";
        }
    
        // Check for straight
        $isStraight = false;
        $straight = $this->checkForStraight($ranks);
        if ($straight !== []) {
            $isStraight = true;
            echo "true";
            $upperEndCard = max($straight);

        }
        var_dump($straight);

        if ($isStraight) {
            echo "The hand is a straight. The highest card is: " . $upperEndCard;
        } else {
            echo "The hand is not a straight.";
        }
        var_dump($krasch);

        // Check for straight flush and royal flush
        if ($isStraight && $isFlush) {
            if ($maxRank === 14) {
                return "Royal Flush";
            } else {
                return "Straight Flush";
            }
        }
    
        // Check for four of a kind
        if (in_array(4, $rankCounts)) {
            return "Four of a Kind";
        }
    
        // Check for full house
        if (in_array(3, $rankCounts) && in_array(2, $rankCounts)) {
            return "Full House";
        }
    
        // Check for flush
        if ($isFlush) {
            return "Flush";
        }
    
        // Check for straight
        if ($isStraight) {
            return "Straight";
        }
    
        // Check for three of a kind
        if (in_array(3, $rankCounts)) {
            return "Three of a Kind";
        }
    
        // Check for two pair
        if (array_count_values($rankCounts)[2] === 2) {
            return "Two Pair";
        }
    
        // Check for one pair
        if (in_array(2, $rankCounts)) {
            return "One Pair";
        }
    
        // If no other hand, it's a high card
        return "High Card";
    }

    public function checkForStraight(array $ranks) : array 
    {
        sort($ranks);

        $previousRank = 0;
        $count = 0;
        $wheel = false;
        foreach ($ranks as $rank) {
            if ($rank == $previousRank) {
                continue;
            } else if ($rank == ++$previousRank) {
                $count++;
            } else {
                if ($previousRank == 6) $wheel = true;
                $count = 1;
                $previousRank = $rank;
            }

            if ($count == 5 || ($rank == 14 && $wheel)) {
                echo "Yeah a striiaght, the highest card is: " . $previousRank;
                $straight = range($previousRank - 4, $previousRank);
                return $straight;
            }
        }
        return [];
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


