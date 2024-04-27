<?php

namespace App\Poker;

use App\Poker\ActionSequence;
use App\Poker\Challenge;
use App\Poker\Hero;
use App\Poker\Table;
use App\Poker\Villain;
use App\Poker\ChallengeDealer;
use App\Poker\ChallengeTable;
use App\Cards\DeckOfCards;
use App\Cards\TexasCardHand;
use App\Poker\HandChecker;
use App\Poker\Game;
use App\Poker\GameEventTrait;

use App\Cards\CardHand;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class Game
{
    public object $challenge;
    public object $hero;
    public object $handChecker;
    public object $villain;
    public object $table;
    public object $dealer;
    public object $deck;

    public function addChallenge(Challenge $challenge)
    {
        $this->challenge = $challenge;
    }
    public function addHero(Hero $hero)
    {
        $this->hero = $hero;
    }
    public function addHandChecker(HandChecker $handChecker)
    {
        $this->handChecker = $handChecker;
    }
    public function addVillain(Villain $villain)
    {
        $this->villain = $villain;
    }
    public function addTable(ChallengeTable $table)
    {
        $this->table = $table;
    }
    public function addDealer(ChallengeDealer $dealer)
    {
        $this->dealer = $dealer;
    }
    public function addDeck(DeckOfCards $deck)
    {
        $this->deck = $deck;
    }

    public function initObjects($handsToPlay, $session) {

        $hero = new Hero();
        $villain = new Villain();
        $handChecker = new HandChecker();
        $villain->addHandChecker($handChecker);
        $challenge = new Challenge($handsToPlay);
        $challenge->addHero($hero);
        $challenge->addVillain($villain);
        $table = new ChallengeTable(25, 50);
        $deck = new DeckOfCards();
        $playerList = [$hero, $villain];
        $dealer = new ChallengeDealer($playerList);
        $dealer->addDeck($deck);
        $table->seatDealer($dealer);
        $dealer->addTable($table);
        $table->seatPlayers($villain, $hero);

        $challenge->addDealer($dealer);
        $challenge->addTable($table);
        $heroStartStack = $hero->getStack();

        $this->addChallenge($challenge);
        $this->addHero($hero);
        $this->addVillain($villain);
        $this->addTable($table);
        $this->addDealer($dealer);
        $this->addHandChecker($handChecker);
        $this->addDeck($deck);

        $session->set("challenge", $challenge);
        $session->set("hero", $hero);
        $session->set("villain", $villain);
        $session->set("table", $table);
        $session->set("dealer", $dealer);
        $session->set("hand_checker", $handChecker);
        $session->set("deck", $deck);
        $session->set("hero_start_stack", $heroStartStack);

    }

    public function getGameState() {
        return [
            "challenge" => $this->challenge,
            "hero" => $this->hero,
            "villain" => $this->villain,
            "table" => $this->table,
            "handChecker" => $this->handChecker,
            "challenge" => $this->challenge,
            "challenge" => $this->challenge,
        ];
    }

    public function getSessionVariables(SessionInterface $session): array
    {
        $hero = $this->hero;
        $villain = $session->get("villain");
        $table = $session->get("table");
        $board = $table->getCardImages();

        return [
            "teddy_hand" => $villain->getImgPaths(),
            "mos_hand" => $hero->getImgPaths(),
            "teddy_stack" => $villain->getStack(),
            "mos_stack" => $hero->getStack(),
            "teddy_pos" => $villain->getPosition(),
            "mos_pos" => $hero->getPosition(),
            "pot_size" => $table->getPotSize(),
            "teddy_bet" => $villain->getCurrentBet(),
            "mos_bet" => $hero->getCurrentBet(),
            "price" => $table->getPriceToPlay(),
            "min_raise" => $table->getMinimumRaiseAllowed(),
            "board" => $board,
            "street" => $table->getStreet(),
            "teddy_last_action" => $villain->getLastAction(),
            "winner" => $this->challenge->getHandWinner(),
            "teddy_hand_strength" => $villain->getStrength(),
            "mos_hand_strength" => $hero->getStrength(), 
        ];
    }


//     public function addToSession(SessionInterface $session, string $key, $value)
// {
//     $session->set($key, $value);
// }
}