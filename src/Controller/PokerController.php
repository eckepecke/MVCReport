<?php

namespace App\Controller;

// use App\Poker\Dealer;
use App\Poker\Game;
// use App\Poker\Player;

// use App\Poker\CommunityCardManager;
// use App\Poker\PotManager;
// use App\Poker\PositionManager;



// use App\Cards\DeckOfCards;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PokerController extends AbstractController
{
    #[Route("/poker", name: "poker_init_get", methods: ['GET'])]
    public function init(): Response
    {
        return $this->render('poker/index.html.twig');
    }

    #[Route("/poker", name: "poker_init_post", methods: ['POST'])]
    public function initCallback(
        Request $request,
        SessionInterface $session
    ): Response {
        $numHands = $request->request->get('num_hands');

        $game = new Game();
        $game->init();

        $session->set("game", $game);

        return $this->redirectToRoute('poker_play');
    }


    #[Route("/poker/play", name: "poker_play", methods: ['GET', 'POST'])]
    public function play(
        Request $request,
        SessionInterface $session
    ): Response {
        $action = $request->request->get('action');
        if ($action === null) {
            $action = $request->request->get('bet');
        }



        $game = $session->get("game");

        $game->prepare($action);


        $data = $game->getTemplateData();
        if ($data["game_over"]) {

        return $this->render('poker/end.html.twig', $data);
        }

        return $this->render('poker/play.html.twig', $data);
    }


    // #[Route("/api/poker", name: "api_poker", methods: ["POST", "GET"])]
    // public function apiPoject(
    //     SessionInterface $session
    // ): Response {
    //     $game = $session->get("game");

    //     if (!$session->has("game")) {
    //         throw new Exception("No game in session!");
    //     }
    //     $data = $game->getTemplateData();

    //     $response = new JsonResponse($data);
    //     $response->setEncodingOptions(
    //         $response->getEncodingOptions() | JSON_PRETTY_PRINT
    //     );
    //     return $response;
    // }
}
