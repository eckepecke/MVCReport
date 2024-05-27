<?php

namespace App\Poker;

class SameHandEvaluator extends HandEvaluator
{
    public function compareHighCard(array $hands): int
    {

        $allHandsSorted = [];
        foreach ($hands as $hand) {
            rsort($hand);
            $allHandsSorted[] = $hand;
        }

        $numPlayers = count($allHandsSorted);
        $amountOfCards = count($allHandsSorted[0]);
        $winningIndex = null;

        // Iterate over each card index
        for ($i = 0; $i < $amountOfCards; $i++) {
            // Assume the first player has the highest card initially
            $highestCard = $allHandsSorted[0][$i];
            $winningIndex = 0;

            // Compare the card with all other players
            for ($j = 1; $j < $numPlayers; $j++) {
                if ($allHandsSorted[$j][$i] > $highestCard) {
                    $highestCard = $allHandsSorted[$j][$i];
                    $winningIndex = $j;
                } elseif ($allHandsSorted[$j][$i] == $highestCard) {
                    // If there's a tie for the current card, continue to the next card
                    $winningIndex = null;
                    break;
                }
            }

            // If there's a clear winner for the current card, break the loop
            if ($winningIndex !== null) {
                break;
            }
        }
        return $winningIndex;
    }

    public function compareOnePair(array $hands): int
    {

        $bestHandIndex = -1;
        $highestPairRank = -1;
        $bestHandKickers = [];

        foreach ($hands as $index => $hand) {
            $rankCounts = array_count_values($hand);
            // Find the pair and kickers
            $pairRank = -1;
            $kickers = [];
            foreach ($rankCounts as $rank => $count) {
                if ($count == 2) {
                    $pairRank = $rank;
                } else {
                    $kickers[] = $rank;
                }
            }

            // If there's no pair, continue to the next hand
            if ($pairRank == -1) {
                continue;
            }

            // Sort kickers in descending order
            rsort($kickers);

            // Compare current hand's pair and kickers to the best hand found so far
            if ($pairRank > $highestPairRank || ($pairRank == $highestPairRank && $kickers > $bestHandKickers)) {
                $highestPairRank = $pairRank;
                $bestHandKickers = $kickers;
                $bestHandIndex = $index;
            }
        }

        return $bestHandIndex;
    }

    public function compareTwoPair(array $hands): int
    {
        $bestHandIndex = -1;
        $highestPairRanks = [-1, -1];
        $bestHandKicker = -1;
    
        foreach ($hands as $index => $hand) {
            // Sort hand in descending order
            rsort($hand);
    
            // Extract pairs and kicker
            $pair1 = $hand[0];
            $pair2 = $hand[2];
            $kicker = $hand[4];
    
            // Compare pairs and kicker to the best hand found so far
            if ($pair1 > $highestPairRanks[0] || ($pair1 == $highestPairRanks[0] && $pair2 > $highestPairRanks[1]) ||
                ($pair1 == $highestPairRanks[0] && $pair2 == $highestPairRanks[1] && $kicker > $bestHandKicker)) {
                $highestPairRanks = [$pair1, $pair2];
                $bestHandKicker = $kicker;
                $bestHandIndex = $index;
            }
        }
    
        return $bestHandIndex;
    }

    public function compareTrips(array $hands): int
    {
    $bestHandIndex = -1;
    $highestTripRank = -1;
    $bestHandKickers = [];

    foreach ($hands as $index => $hand) {
        // Sort hand in descending order
        rsort($hand);

        // Extract trip rank and kickers
        $tripRank = $hand[0];
        $kickers = [$hand[3], $hand[4]];

        // Compare trip rank and kickers to the best hand found so far
        if ($tripRank > $highestTripRank || ($tripRank == $highestTripRank && $kickers > $bestHandKickers)) {
            $highestTripRank = $tripRank;
            $bestHandKickers = $kickers;
            $bestHandIndex = $index;
        }
    }

    return $bestHandIndex;
    }

    public function compareQuads(array $hands): int
    {
    $bestHandIndex = -1;
    $highestQuadRank = -1;
    $bestHandKicker = -1;

    foreach ($hands as $index => $hand) {
        // Sort hand in descending order
        rsort($hand);

        // Extract quad rank and kicker
        $quadRank = $hand[0];
        $kicker = $hand[4];

        // Compare quad rank and kicker to the best hand found so far
        if ($quadRank > $highestQuadRank || ($quadRank == $highestQuadRank && $kicker > $bestHandKicker)) {
            $highestQuadRank = $quadRank;
            $bestHandKicker = $kicker;
            $bestHandIndex = $index;
        }
    }

    return $bestHandIndex;
    }

    public function compareFullHouses(array $hands): int
    {
    $bestHandIndex = -1;
    $highestTripRank = -1;
    $highestPairRank = -1;

    foreach ($hands as $index => $hand) {
        $rankCounts = array_count_values($hand);
        
        $tripRank = -1;
        $pairRank = -1;

        // Identify the trip and pair ranks
        foreach ($rankCounts as $rank => $count) {
            if ($count == 3) {
                $tripRank = $rank;
            } elseif ($count == 2) {
                $pairRank = $rank;
            }
        }

        // If there's no valid full house, continue to the next hand
        if ($tripRank == -1 || $pairRank == -1) {
            continue;
        }

        // Compare current hand's trips and pair to the best hand found so far
        if ($tripRank > $highestTripRank || ($tripRank == $highestTripRank && $pairRank > $highestPairRank)) {
            $highestTripRank = $tripRank;
            $highestPairRank = $pairRank;
            $bestHandIndex = $index;
        }
    }

    return $bestHandIndex;
    }

    public function compareStraights(array $handRanks): int
    {
        foreach ($handRanks as $index => $hand) {
            $bestHandIndex = -1;
            $highestStraightRank = -1;

            foreach ($handRanks as $index => $hand) {
                // Sort hand in descending order and remove duplicates
                $ranks = array_unique($hand);
                rsort($ranks);

                $straightHighCard = -1;

                for ($i = 0; $i <= count($ranks) - 5; $i++) {
                    if ($ranks[$i] - 4 == $ranks[$i + 4]) {
                        $straightHighCard = $ranks[$i];
                        break;
                    }
                }

                // Special case for wheel (A-2-3-4-5)
                if ($ranks === [14,5,4,3,2]) {
                    $isStraight = true;
                    $straightHighCard = 5;
                }

                // Compare current hand's straight high card to the best hand found so far
                if ($straightHighCard > $highestStraightRank) {
                    $highestStraightRank = $straightHighCard;
                    $bestHandIndex = $index;
                }
            }
        }


        return $bestHandIndex;
    }
    
    public function compareFlushes(array $handRanks, array $suitRanks): int
    {
        $flushCardsList = [];
        foreach ($handRanks as $index => $handRank) {
            $flushCardsList[] = $this->getFlushCards($handRank, $suitRanks[$index]);
        }
    
        // Debug output
        foreach ($flushCardsList as $index => $flushCards) {
            echo "Flush Cards " . ($index + 1) . ": " . implode(", ", $flushCards) . PHP_EOL;
        }
    
        // Compare flushes
        $bestIndex = 0;
        for ($i = 1; $i < count($flushCardsList); $i++) {
            for ($j = 0; $j < count($flushCardsList[$bestIndex]); $j++) {
                if ($flushCardsList[$i][$j] > $flushCardsList[$bestIndex][$j]) {
                    $bestIndex = $i;
                    break;
                } elseif ($flushCardsList[$i][$j] < $flushCardsList[$bestIndex][$j]) {
                    break;
                }
            }
        }
    
        return $bestIndex;
    }
    
    public function getFlushCards(array $handRanks, array $suitRanks): array
    {
        $suitsCount = array_count_values($suitRanks);
        $maxSameSuitCount = max($suitsCount);
        $mostFrequentSuit = array_search($maxSameSuitCount, $suitsCount);
    
        // Debug output
        echo "Most Frequent Suit: " . $mostFrequentSuit . PHP_EOL;
    
        // Get all cards of the most frequent suit
        $flushCards = [];
        foreach ($suitRanks as $index => $suit) {
            if ($suit == $mostFrequentSuit) {
                $flushCards[] = $handRanks[$index];
            }
        }
        rsort($flushCards);
        return $flushCards;
    }
}