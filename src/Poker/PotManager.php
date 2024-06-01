<?php

namespace App\Poker;

/**
 * Class PotManager
 *
 * This class manages the pot in a poker game, handling the addition of chips, 
 * resetting the pot, and charging blinds.
 */
class PotManager
{
    /**
     * @var int The current size of the pot.
     */
    protected int $pot;

    /**
     * PotManager constructor.
     *
     * Initializes the PotManager with the given initial pot size.
     *
     * @param int $chips The initial amount of chips in the pot.
     */
    public function __construct($chips)
    {
        $this->pot = $chips;
    }

    /**
     * Adds chips to the pot from each player's current bet.
     *
     * @param array $state The current state of the game, including the players.
     * @return void
     */
    public function addChipsToPot(array $state): void
    {
        $players = $state["players"];
        $hero = $state["hero"];
        $heroBet = $hero->getCurrentBet();

        if ($hero->isAllin()) {
            foreach ($players as $player) {
                if($player->isActive()) {
                    $actual = $player->getCurrentBet();

                    $player->setCurrentBet($heroBet);
                    $diff = $actual - $heroBet;

                    $player->takePot($diff);
                }
                $chips = $player->getCurrentBet();
                echo"Ruffy";
                var_dump($chips);
                $this->pot += $chips;
            }
            return;
        }

        foreach ($players as $player) {
            $chips = $player->getCurrentBet();
            $this->pot += $chips;
        }
    }

    /**
     * Gets the current size of the pot.
     *
     * @return int The current pot size.
     */
    public function getPotSize(): int
    {
        return $this->pot;
    }

    /**
     * Resets the pot size to zero.
     *
     * @return void
     */
    public function resetPot(): void
    {
        $this->pot = 0;
    }

        /**
     * Charges blinds to players based on their position.
     *
     * The blind amounts are set in an array where the key represents the position
     * and the value represents the amount to be paid.
     *
     * @param array $players An array of player objects.
     * @return void
     */
    public function chargeBlinds(array $players): void
    {
        $blindArray = [
            0 => 100,
            1 => 200,
            2 => 400
        ];
        foreach ($players as $player) {
            $pos = $player->getPosition();
            $player->payBlind($blindArray[$pos]);
        }
    }
}
