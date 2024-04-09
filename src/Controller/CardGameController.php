<?php

namespace App\Controller;

use App\Cards\Card;
use App\Cards\CardGraphic;
use App\Cards\CardHand;
use App\Cards\DeckOfCards;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardGameController extends AbstractController
{
    #[Route("/session", name: "session")]
    public function sessionCheck(
        SessionInterface $session
    ): Response {

        if (!$session->has("deck")) {
            $deck = new DeckOfCards();
            $session->set("deck", $deck);
            $session->set("remaining", $deck->size());
        }

        $data = [
            "session" => $session,
        ];
        return $this->render('session.html.twig', $data);
    }

    #[Route("/session/delete", name: "session_delete")]
    public function sessionDelete(
        Request $request,
        SessionInterface $session
    ): Response {
        $session->invalidate();
        $this->addFlash(
            'notice',
            'Session data was deleted!'
        );
        return $this->render('session/delete.html.twig');
    }

    #[Route("/card/empty_deck", name: "empty_deck")]
    public function emptyDeck(SessionInterface $session): Response
    {

        $deck = $session->get("deck");

        $data = [
            "remaining" => $deck->size()
        ];

        return $this->render('card/deck/empty_deck.html.twig', $data);
    }

    #[Route("/card", name: "card")]
    public function home(): Response
    {
        return $this->render('card/card.html.twig');
    }

    #[Route("/card/deck", name: "deck")]
    public function deck(): Response
    {

        $deck = new DeckOfCards();


        $data = [
            "cards" => $deck->getImgNames()
        ];

        return $this->render('card/deck.html.twig', $data);
    }

    #[Route("/card/deck/shuffle", name: "shuffle")]
    public function shuffledDeck(SessionInterface $session): Response
    {

        $deck = new DeckOfCards();
        $deck->shuffleDeck();
        $session->set("deck", $deck);
        $session->set("remaining", $deck->size());


        $data = [
            "cards" => $deck->getImgNames()
        ];

        return $this->render('card/deck/shuffle.html.twig', $data);
    }

    #[Route("/card/deck/draw", name: "deck_draw")]
    public function draw(SessionInterface $session): Response
    {

        if ($session->has("deck")) {
            $deck = $session->get("deck");
        } else {
            $deck = new DeckOfCards();
            $session->set("deck", $deck);
            $session->set("remaining", $deck->size());
        }

        $remains = $session->get("remaining");

        if ($remains <= 0) {
            $redirectRoute = $this->generateUrl('empty_deck');
            return new RedirectResponse($redirectRoute);
        }

        $card = $deck->drawOne();
        $session->set("remaining", $deck->size());



        $drawnCardValue = $card->getValue();

        $goodCards = [
            "jack", "queen", "king"
        ];

        $aces = "ace";

        if (in_array($drawnCardValue, $goodCards) || $drawnCardValue == $aces) {
            $reaction = "good_hand.png";
        } else {
            $reaction = "bad_hand.png";
        }


        $data = [
            "imgPath" => $card->getImgName(),
            "remaining" => $deck->size(),
            "reaction" => $reaction,
            "value" => $drawnCardValue
        ];

        return $this->render('card/deck/draw.html.twig', $data);
    }

    #[Route("/card/deck/draw/{num<\d+>}", name: "draw_many")]
    public function drawMany(int $num, SessionInterface $session): Response
    {

        if ($session->has("deck")) {
            $deck = $session->get("deck");

        } else {
            $deck = new DeckOfCards();

            $session->set("deck", $deck);
            $session->set("remaining", $deck->size());
        }

        $remains = $session->get("remaining");

        if ($num > $remains) {
            $redirectRoute = $this->generateUrl('empty_deck');
            return new RedirectResponse($redirectRoute);
        }

        $deck->shuffleDeck();
        $cards = $deck->drawMany($num);
        $session->set("remaining", $deck->size());
        $imgPaths = [];
        foreach ($cards as $card) {
            $imgPaths[] = $card->getImgName();
        }


        $data = [
            "session" => $session,
            "imgPaths" => $imgPaths
        ];

        return $this->render('card/deck/draw_more.html.twig', $data);
    }
}
