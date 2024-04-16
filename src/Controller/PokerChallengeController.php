<?php

namespace App\Controller;

use App\Poker\Challenge;
use App\Poker\Hero;
use App\Poker\Table;
use App\Poker\Villain;
use App\Poker\ChallengeDealer;
use App\Poker\ChallengeTable;
use App\Cards\DeckOfCards;



use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PokerChallengeController extends AbstractController
{
    // #[Route("/game", name: "game")]
    //     public function home(): Response
    //     {
    //         $hero = new Hero;
    //         $villain = new Villain;

    //         $challenge = new Challenge(4);

    //         $challenge->addHero($hero);
    //         $challenge->addVillain($villain);

    //         $duration = $challenge->getDuration();

    //         $heroName = $challenge->getHeroName();
    //         $villainName = $challenge->getVillainName();


    //         $data = [
    //             "header" => "Welcome to the pokerchallenge",
    //             "duration" => $duration,
    //             "hero" => $heroName,
    //             "villain" => $villainName
    //         ];

    //         return $this->render('game/game.html.twig', $data);
    //     }

        #[Route("/game", name: "game_init_get", methods: ['GET'])]
        public function init(): Response
        {
            return $this->render('poker/game.html.twig');
        }


    #[Route("/game", name: "game_init_post", methods: ['POST'])]
    public function initCallback(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $handsToPlay = $request->request->get('num_hands');
        $hero = new Hero();
        $villain = new Villain();
        $challenge = new Challenge($handsToPlay);
        $challenge->addHero($hero);
        $challenge->addVillain($villain);
        $table = new ChallengeTable(25, 50);
        $deck = new DeckOfCards();
        $playerList = [$hero, $villain];

        $dealer = new ChallengeDealer($playerList);
        var_dump($dealer);
        $dealer->addDeck($deck);
        $table->seatDealer($dealer);


        $dealer->dealHoleCards();

        $data = [
            "header" => "Welcome to the pokerchallenge",
            "duration" => $challenge->getDuration(),
            "hero" => $challenge->getHeroName(),
            "villain" => $challenge->getVillainName(),
            "hands_played" => $challenge->getHandsPlayed(),
            "pot_size" => $table->getPotSize(),
            "teddy_hand" => $villain->getHoleCards(),
            "mos_hand" => $hero->getHoleCards()
        ];

        $session->set("challenge", $challenge);
        $session->set("hero", $hero);
        $session->set("villain", $villain);
        $session->set("table", $table);
        $session->set("dealer", $dealer);
        $session->set("deck", $deck);

        return $this->redirectToRoute('poker_play');

        //return $this->render('/play.html.twig', $data);


    }

    #[Route("/play", name: "poker_play", methods: ['GET'])]
    public function play(
        SessionInterface $session
    ): Response
    {

        $challenge = $session->get("challenge");
        $hero = $session->get("hero");
        $villain = $session->get("villain");
        $table = $session->get("table");
        $dealer = $session->get("dealer");
        $deck = $session->get("deck");
        // var_dump($dealer);
        $dealer->moveButton();
        $blinds = $dealer->chargeAntes(25, 50);
        $table->addChipsToPot($blinds);

        $data = [
            "teddy_hand" => $villain->getImgPaths(),
            "mos_hand" => $hero->getImgPaths(),
            "pot_size" => $table->getPotSize()
        ];

        return $this->render('poker/play_test.html.twig', $data);
    }
}