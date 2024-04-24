<?php

namespace App\Poker;

/**
 * A trait that takes an array and returns the first Texas true item.
 */
trait TexasHandTrait
{
    protected string $currentStrength;

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
