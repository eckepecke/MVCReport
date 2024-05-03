<?php

namespace App\Controller;

use Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
//use Symfony\Component\HttpFoundation\Exception;

use App\Cards\DeckOfCards;

class DeckControllerJson
{
    #[Route("/api/deck", name: "api_deck")]
    public function jsonDeck(): Response
    {
        $deck = new DeckOfCards();
        $cards = $deck->getCards();
        $formattedCards = [];
        foreach ($cards as $card) {
            $formattedCards[] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
                'cardString' => $card->getCardString(),
                'imgPath' => $card->getImgName()
            ];
        }

        $response = new JsonResponse($formattedCards);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }


    #[Route("/api/deck/shuffle", name: "api_shuffle", methods: ["POST", "GET"])]
    public function apiShuffle(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();
        $deck->shuffleDeck();
        $session->set("deck", $deck);

        $cards = $deck->getCards();
        $formattedCards = [];
        foreach ($cards as $card) {
            $formattedCards[] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
                'cardString' => $card->getCardString(),
                'imgPath' => $card->getImgName()
            ];
        }

        $response = new JsonResponse($formattedCards);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/draw", name: "api_draw", methods: ["POST", "GET"])]
    public function apiDrawOne(
        SessionInterface $session
    ): Response {
        $deck = $session->get("deck") ?? throw new Exception("No deck in session!");

        $remains = $deck->size();

        if ($remains <= 0) {
            throw new Exception("No cards in deck!");
        }
        $deck->drawOne();
        $session->set("deck", $deck);

        $cards = $deck->getCards();
        $formattedCards = [];
        foreach ($cards as $card) {
            $formattedCards[] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
                'cardString' => $card->getCardString(),
                'imgPath' => $card->getImgName()
            ];
        }

        $response = new JsonResponse($formattedCards);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/draw/{num<\d+>}", name: "api_draw_more", methods: ["POST", "GET"])]
    public function apiDrawMore(
        int $num,
        SessionInterface $session
    ): Response {
        $deck = $session->get("deck") ?? throw new Exception("No deck in session!");

        $remains = $deck->size();

        if ($num > $remains) {
            throw new Exception("Only {$remains} cards in deck!");
        }
        $deck->drawMany($num);
        $session->set("deck", $deck);

        $cards = $deck->getCards();
        $formattedCards = [];
        foreach ($cards as $card) {
            $formattedCards[] = [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
                'cardString' => $card->getCardString(),
                'imgPath' => $card->getImgName()
            ];
        }

        $response = new JsonResponse($formattedCards);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
