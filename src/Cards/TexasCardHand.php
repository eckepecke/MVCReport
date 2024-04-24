<?php

namespace App\Cards;

class TexasCardHand extends CardHand
{
    private $currentStrength;

    public function updateStrength(array $boolValues): void
    {
        echo "HÄÄÄR";

        foreach ($boolValues as $key => $value) {
            //var_dump($value);
            if ($value !== false) {
                echo "The first non-false value is '$key'.";
                $this->currentStrength = $key;
                break; // Stop the loop once the first non-false value is found
            }
        }

        //
        var_dump($this->currentStrength);
    }

    public function getStrength()
    {

    }
}
