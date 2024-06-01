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

    #[Route('/api/update_hero_name', name: 'update_name', methods: ["POST", "GET"])]
    public function updateHeroName(
        SessionInterface $session,
        Request $request
    ): Response {

        $name = $request->request->get('name');
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

        $heroData = [
            'name' => $data["hero_name"],
            'stack' => $data["hero_stack"],
            'position' => $data["hero_pos"],
            'hero_hand' => $data["hero_hand"],
            'current_bet' => $data["heroBet"],
        ];

        $response = new JsonResponse($heroData);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/peek", name: "peek_cards")]
    public function apiOpponent1(
        SessionInterface $session
    ): Response {
        $game = $session->get("game");

        if (!$session->has("game")) {
            throw new Exception("No game in session!");
        }
        $data = $game->getTemplateData();
        $opponent1Name = $data["opp_1_name"];
        $opponent1Hand = $data["opponent1Hand"];

        $opponent2Name = $data["opp_2_name"];
        $opponent2Hand = $data["opponent1Hand"];

        $opponentData = [
            'name_one' => $opponent1Name,
            'hand_one' => $opponent1Hand,
            'name_two' => $opponent2Name,
            'hand_two' => $opponent2Hand,
        ];

        $response = new JsonResponse($opponentData);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/progress", name: "progress")]
    public function progress(
        SessionInterface $session
    ): Response {
        $game = $session->get("game");

        if (!$session->has("game")) {
            throw new Exception("No game in session!");
        }
        $data = $game->getTemplateData();
        $heroStack = $data["hero_stack"];
        $handsPlayed = $data["hands_played"];

        $target = 10000;
        $missing = $target - $heroStack;

        $toBePlayed = 10;
        $remaining = $toBePlayed - $handsPlayed;


        $json = [
            'money_missing' => $missing,
            'remaining_hands' => $remaining,
        ];

        $response = new JsonResponse($json);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
