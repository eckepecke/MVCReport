<?php

namespace App\Poker;

// use App\Poker\CommunityCardManager;
// use App\Poker\PotManager;
// use App\Poker\PositionManager;


/**
 * Manages the game.
 */
class Manager
{
    private object $CCManager;
    private object $potManager;
    private object $positionManager;
    private object $cardManager;
    private object $betManager;
    private object $streetManager;



    public function addCCM(CommunityCardManager $manager): void
    {
        $this->CCManager = $manager;
    }

    public function addPotManager(PotManager $manager): void
    {
        $this->potManager = $manager;
    }

    public function addPositionManager(PositionManager $manager): void
    {
        $this->positionManager = $manager;
    }

    public function addCardManager(CardManager $manager): void
    {
        $this->cardManager = $manager;
    }

    public function addBetManager(BetManager $manager): void
    {
        $this->betManager = $manager;
    }

    public function addStreetManager(StreetManager $manager): void
    {
        $this->streetManager = $manager;
    }

    public function access(string $manager): object
    {
        return $this->$manager;
    }

    public function dealStartingHands($state)
    {
        // if state says so:
    
        if($state["newHand"] === true) {
            $this->cardManager->shuffleCards();
            $this->cardManager->dealStartHandToAllPlayers($state["players"]);
        }
    }

    public function dealCommunityCards($state)
    {
        // Will price check be enough in the future?
        // ?????????????????????????????????????
        $priceToPlay = $this->betManager->getPriceToPlay($state);
        if ($priceToPlay === 0) {
            $street = $this->streetManager->getStreet();
            $cardsDealt = $this->CCManager->cardsDealt();
            $cards = $this->cardManager->dealCommunityCards($street, $cardsDealt);
            $this->CCManager->register($cards);
        }
    }
}