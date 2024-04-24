<?php

namespace App\Poker;

/**
 * A trait implementing histogram for integers.
 */
trait TexasHandTrait
{
    protected $currentStrength;

    public function updateStrength(array $boolValues): void
    {
        foreach ($boolValues as $key => $value) {
            if ($value !== false) {
                echo "The first non-false value is '$key'.";
                $this->currentStrength = $key;
                break;
            }
        }
    }


    public function getStrength(): string
    {
        return $this->currentStrength;
    }
}
