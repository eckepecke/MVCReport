<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class PokerJsonController extends AbstractController
{
    #[Route("/api/poker_data", name: "api_poker", methods: ["POST", "GET"])]
    public function apiPoject(
        SessionInterface $session
    ): Response {
        $game = $session->get("game");

        if (!$session->has("game")) {
            throw new Exception("No game in session!");
        }
        $data = $game->getTemplateData();

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route('/api/update_hero_name{name}', name: 'update_name', methods: ["POST", "GET"])]
    public function updateHeroName(
        SessionInterface $session,
        string $name
    ): Response {
        $game = $session->get("game");

        if (!$session->has("game")) {
            throw new Exception("No game in session!");
        }

        $state = $game->getGameState();
        $state["hero"]->setName($name);

        return $this->redirectToRoute('hero_data');
    }

    #[Route("/api/hero_data", name: "hero_data")]
    public function apiHero(
        SessionInterface $session
    ): Response {
        $game = $session->get("game");

        if (!$session->has("game")) {
            throw new Exception("No game in session!");
        }
        $data = $game->getTemplateData();
        $heroName = $data["hero_name"];

        $heroData = [
            'name' => $heroName,
        ];

        $response = new JsonResponse($heroData);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/o1_data", name: "hero_data")]
    public function apiOpponent1(
        SessionInterface $session
    ): Response {
        $game = $session->get("game");

        if (!$session->has("game")) {
            throw new Exception("No game in session!");
        }
        $data = $game->getTemplateData();
        $opponentName = $data["opp_1_name"];

        $opponentData = [
            'name' => $opponentName,
        ];

        $response = new JsonResponse($opponentData);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
