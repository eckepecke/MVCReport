<?php

namespace App\Controller;

use App\Poker\GameEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PokerChallengeController extends AbstractController
{
    #[Route("/preflop", name: "preflop", methods: ['GET'])]
    public function preflop(
        Request $request,
        SessionInterface $session
    ): Response {
        $game = $session->get("game");
        $session = $request->getSession();
        // $gameState = $game->getGameState();
        // $currentDetails = $game->getSessionVariables($session);

        if ($game->challenge->challengeComplete()) {
            $startStack = $game->hero->getStartStack();
            $data = $game->getSessionVariables($session);
            $data["result"] = $game->challenge->getResult($startStack, $game->hero->getStack());
            return $this->render('poker/end_game.html.twig', $data);
        }
        $game->preflopPrep();
        // $table = $gameState['table'];
        // $villain = $gameState['villain'];

        if ($game->table->getSbPlayer() === $game->villain) {
            $action = $game->villain->randActionRFI();
            $game->villainUnOpenedPot($action);
            if ($action === "fold") {
                $data = $game->getSessionVariables($session);
                return $this->render('poker/teddy_fold.html.twig', $data);
            }
        }
        $data = $game->getSessionVariables($session);
        return $this->render('poker/test.html.twig', $data);
    }

    #[Route("/poker/session/delete", name: "session_delete")]
    public function sessionDelete(
        SessionInterface $session
    ): Response {
        $session->invalidate();
        $this->addFlash(
            'notice',
            'Session data was deleted!'
        );
        return $this->render('poker/delete.html.twig');
    }

    #[Route("/game", name: "game_init_get", methods: ['GET'])]
    public function init(): Response
    {
        return $this->render('poker/game.html.twig');
    }

    #[Route("/game", name: "game_init_post", methods: ['POST'])]
    public function initCallback(
        Request $request,
        SessionInterface $session
    ): Response {
        $game = new GameEvents();

        $handsToPlay = $request->request->get('num_hands');
        $session = $request->getSession();
        $game->initObjects($handsToPlay, $session);
        $session->set("game", $game);
        return $this->redirectToRoute('preflop');
    }

    #[Route("/game/fold", name: "fold", methods: ['GET', 'POST'])]
    public function fold(
        SessionInterface $session
    ): Response {
        $game = $session->get("game");
        $game->someoneFolded();

        return $this->redirectToRoute('preflop');
    }

    #[Route("/game/call", name: "call", methods: ['GET', 'POST'])]
    public function call(
        SessionInterface $session
    ): Response {
        $game = $session->get("game");

        $game->betWasCalled();
        $street = $game->table->getStreet();

        if($game->dealer->playersAllIn() || $street === 4) {
            $game->dealer->dealToShowdown();
            return $this->redirectToRoute('showdown');
        }
        $game->table->dealCorrectCardAfterCall();
        $game->villainCouldBetFromBigBlind();

        $data = $game->getSessionVariables($session);
        return $this->render('poker/test.html.twig', $data);
    }

    #[Route("/game/bet", name: "bet", methods: ['POST'])]
    public function bet(
        Request $request,
        SessionInterface $session
    ): Response {
        $heroBet = $request->request->get('bet');
        $villain = $session->get("villain");
        $hero = $session->get("hero");

        $hero->bet($heroBet);
        $action = $villain->actionFacingBet();
        //$redirectRoute = $action;

        if($action === "fold") {
            return $this->redirectToRoute('fold');
        }

        if($action === "call") {
            return $this->redirectToRoute('call');
        }

        if($heroBet > $villain->getStack() || $hero->getStack() <= 0) {
            return $this->redirectToRoute('call');
        }
        $villain->raise($heroBet);

        $data = $game->getSessionVariables($session);
        return $this->render('poker/test.html.twig', $data);
    }

    #[Route("/game/check", name: "check", methods: ['POST'])]
    public function check(
        SessionInterface $session
    ): Response {
        $game = $session->get("game");
        $game->heroChecked();
        $data = $game->getSessionVariables($session);
        if ($game->table->getStreet() === 1) {
            return $this->redirectToRoute('showdown');
        }
        return $this->render('poker/test.html.twig', $data);
    }

    #[Route("/game/showdown", name: "showdown", methods: ['GET', 'POST'])]
    public function showdown(
        Request $request,
        SessionInterface $session
    ): Response {
        $game = $session->get("game");
        $session = $request->getSession();
        $game->compareHands($session);
        $data = $game->getSessionVariables($session);
        $game->dealer->resetForNextHand();
        $game->challenge->incrementHandsPlayed();
        return $this->render('poker/showdown.html.twig', $data);
    }

    #[Route("/api/game", name: "api_game", methods: ["POST", "GET"])]
    public function apiPoker(
        SessionInterface $session
    ): Response {
        $game = $session->get("game");

        if (!$session->has("challenge")) {
            throw new Exception("No challenge in session!");
        }

        $data = $game->getSessionVariables($session);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
