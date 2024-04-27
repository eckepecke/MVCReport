<?php

namespace App\Controller;

use App\FlopAndGo\Challenge;
use App\FlopAndGo\Game;
use App\FlopAndGo\HandChecker;
use App\FlopAndGo\Hero;
use App\FlopAndGo\Moderator;
use App\FlopAndGo\SpecialDealer;
use App\FlopAndGo\SpecialTable;
use App\FlopAndGo\Villain;
use App\Cards\DeckOfCards;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GambleController extends AbstractController
{

    #[Route("/game", name: "game_init_get", methods: ['GET'])]
    public function init(): Response
    {
        return $this->render('gamble/game.html.twig');
    }

    #[Route("/game", name: "gamble_init_post", methods: ['POST'])]
    public function initCallback(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $numHands = $request->request->get('num_hands');

        $game = new Game();
        $hero = new Hero();
        $villain = new Villain();
        $table = new SpecialTable();
        $dealer = new SpecialDealer();
        $deck = new DeckOfCards();
        $handChecker = new HandChecker();
        $challenge = new Challenge($numHands);

        $dealer->addDeck($deck);
        $dealer->addTable($table);
        $table->seatPlayers($hero, $villain);
        $game->addHero($hero);
        $game->addVillain($villain);
        $game->addTable($table);
        $game->addDealer($dealer);
        $game->addHandChecker($handChecker);
        $game->addChallenge($challenge);
        $dealer->getPlayerList([$hero, $villain]);

        $session->set("game", $game);

        return $this->redirectToRoute('gamble_play');
    }


    #[Route("/game/gamble/play", name: "gamble_play", methods: ['GET'])]
    public function play(
        SessionInterface $session
    ): Response
    {
        $game = $session->get("game");
        $game->play();
        $data = $game->getGameState();
        return $this->render('gamble/play.html.twig', $data);
    }

}