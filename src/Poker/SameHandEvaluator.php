<?php

namespace App\Poker;

class SameHandEvaluator extends HandEvaluator
{
    public function compareHighCard(array $hands)
    {

        $allHandsSorted = [];
        foreach ($hands as $hand) {
            $ranksAndSuits = $this->extractRanksAndSuits($hand);
            $ranks = $ranksAndSuits[0];
            sort($ranks);
            $allHandsSorted[] = $ranks;
        }

        $numPlayers = count($allHandsSorted);
        $amountOfCards = count($allHandsSorted[0]);
        if ($numPlayers == 2) {
        echo"2PlayersCompared";

            // Compare the two hands directly
            for ($i = 0; $i < $amountOfCards; $i++) {
                if ($allHandsSorted[0][$i] > $allHandsSorted[1][$i]) {
                    $winningIndex = 0;
                    break;
                } elseif ($allHandsSorted[1][$i] > $allHandsSorted[0][$i]) {
                    $winningIndex = 1;
                    break;
                }
            }
    

        } 
        if ($numPlayers > 2) {
            echo"3PlayersCompared";
            // Comparing sorted arrays index by index
            $winningIndex = null;
            
            for ($i = 0; $i < count($allHandsSorted[0]); $i++) {
                if ($allHandsSorted[0][$i] > $allHandsSorted[1][$i] && $allHandsSorted[0][$i] > $allHandsSorted[2][$i]) {
                    $winningIndex = 0;
                    break;
                } elseif ($allHandsSorted[1][$i] > $allHandsSorted[0][$i] && $allHandsSorted[1][$i] > $allHandsSorted[2][$i]) {
                    $winningIndex = 1;
                    break;
                } elseif ($allHandsSorted[2][$i] > $allHandsSorted[0][$i] && $allHandsSorted[2][$i] > $allHandsSorted[1][$i]) {
                    $winningIndex = 2;
                    break;
                }
            }
        }

        return $winningIndex;
    }


}