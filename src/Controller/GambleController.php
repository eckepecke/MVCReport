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


    #[Route("/game/gamble/play", name: "gamble_play", methods: ['GET', 'POST'])]
    public function play(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $action = $request->request->get('action');
        $game = $session->get("game");
        $action = $game->getUserInput($request);
        //$action = $request->request->get('fold');

        // if ($action === NULL) {
        //     $action = $request->request->get('check');
        // }
        // if ($action === NULL) {
        //     $action = $request->request->get('call');
        // }
        // if ($action === NULL) {
        //     $action = $request->request->get('bet');
        // }
        // if ($action === NULL) {
        //     $action = $request->request->get('next');
        // }
        var_dump($action);




        $game->play($action);

        //$game->play($action);
        $data = $game->getGameState();

        if ($game->isNewHand()) {
            $action = null;
            $game->play($action);
        }

        $data = $game->getGameState();

        return $this->render('gamble/play.html.twig', $data);
    }

}