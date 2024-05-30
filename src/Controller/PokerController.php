<?php

namespace App\Controller;

use App\Poker\Game;
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
    #[Route("/proj", name: "poker_init_get", methods: ['GET'])]
    public function init(): Response
    {
        return $this->render('poker/index.html.twig');
    }

    #[Route("/proj/about", name: "about_proj", methods: ['GET'])]
    public function about(): Response
    {
        return $this->render('poker/about.html.twig');
    }

    #[Route("/proj", name: "poker_init_post", methods: ['POST'])]
    public function initCallback(
        Request $request,
        SessionInterface $session
    ): Response {

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
}
