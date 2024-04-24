<?php

namespace App\Poker;

class HandChecker
{

    private $strengthArray;
    private $rankMapping;


    public function __construct()
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
    }

    public function evaluateHand($cards) {
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
        var_dump($numRanks);
        // var_dump($maxFrequency);
        // var_dump($maxFrequencyRank);


        // var_dump($minRank);
        //var_dump($numRanks);


        echo "value ovan";


        $suitsCount = array_count_values($suits);
        $numSuits = count($suitsCount);
        $maxSameSuitCount = max($suitsCount);
        // var_dump($suitsCount);
        // var_dump($numSuits);
        // var_dump($maxSameSuitCount);
        //$minRank = min(array_keys($rankCounts));

        echo "suit ovan";

        $isStraight = false;
        $isFlush = false;
    
        // Check for flush
        if ($maxSameSuitCount >= 5) {
            $isFlush = true;
            $this->strengthArray['flush'] = true;
            echo "hand has flush";
        }
    
        // Check for straight
        $isStraight = false;
        $straight = $this->checkForStraight($ranks);
        if ($straight !== []) {
            $isStraight = true;
            $this->strengthArray['straight'] = true;
            echo "true";
            $upperEndCard = max($straight);
        }
        var_dump($straight);

        // Check for straight flush and royal flush
        if ($isStraight && $isFlush) {
            if ($upperEndCard === 14) {
                $this->strengthArray['royalFlush'] = true;
                echo "Royal Flush";
            } else {
                $this->strengthArray['straightFlush'] = true;
                echo "Straight Flush";
            }
        }

    
        // Check for four of a kind
        if (in_array(4, $rankCounts)) {
            $this->strengthArray['fourOfAKind'] = true;
            echo "Four of a Kind";
        }
    
        // Check for full house
        if (in_array(3, $rankCounts) && in_array(2, $rankCounts)) {
            $this->strengthArray['fullHouse'] = true;
            echo "Full House";
        }
    
        // Check for flush
        if ($isFlush) {
            $this->strengthArray['flush'] = true;
            echo "Flush";
        }
    
        // Check for straight
        if ($isStraight) {
            $this->strengthArray['straight'] = true;
            echo "Straight";
        }
    
        // Check for three of a kind
        if (in_array(3, $rankCounts)) {
            $this->strengthArray['threeOfAKind'] = true;
            echo "Three of a Kind";
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
            $this->strengthArray['twoPair'] = true;
            echo "Two pairs found!";
        } else {
            // Two pairs do not exist
            echo "No two pairs found!";
        }
        if ($pairCount > 0 && $pairCount < 2) {
            $this->strengthArray['onePair'] = true;
            echo "one pair";

        }
        $this->strengthArray['highCard'] = true;
        echo "High Card";
        var_dump($this->strengthArray);
        return $this->strengthArray;
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

    public function resetStrengthArray () : void {
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

    // public findStrongestHand($handOne, $handTwo){
    //     if 
    // }
}



// Example usage:
// $hand = array(
//     array("rank" => 2, "suit" => "Hearts"),
//     array("rank" => 3, "suit" => "Hearts"),
//     array("rank" => 4, "suit" => "Hearts"),
//     array("rank" => 5, "suit" => "Hearts"),
//     array("rank" => 6, "suit" => "Hearts")
// );


