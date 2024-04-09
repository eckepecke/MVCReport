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
        Request $request,
        SessionInterface $session
    ): Response
    {

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
        $session->invalidate(); // This will destroy the session
    
        return $this->redirectToRoute('session');
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

    #[Route("/card/test", name: "test")]
    public function home(): Response
    {
        return $this->render('cards/test.html.twig');
    }

    #[Route("/card/peel", name: "peel_card")]
    public function testPeelCard(): Response
    {
        //$die = new Card();
        $card = new CardGraphic();

        $data = [
            "card" => $card->getCard(),
            "imgPath" => $card->getImgName()
        ];

        return $this->render('cards/peel.html.twig', $data);

    }

    #[Route("/card/testhand", name: "test_hand")]
    public function testHand(): Response
    {

        $card = new CardGraphic();
        $hand = new CardHand();
        $hand->add($card);

        $data = [
            "cards" => $hand->getCardValues(),
            "imgPaths" => $hand->getImgNames(),
            // "card" => $hand->pull(),
            "numOfCards" => $hand->getNumberCards()
        ];

        return $this->render('cards/testhand.html.twig', $data);

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
            $remains = $session->get("remaining");

        } else {
            $deck = new DeckOfCards();
            $session->set("deck", $deck);
            $session->set("remaining", $deck->size());
        }
        if ($remains <= 0) {
            //throw new \Exception("Only {$remains} cards in deck!");
            $redirectRoute = $this->generateUrl('empty_deck');
            return new RedirectResponse($redirectRoute);
        }

//        $deck->shuffleDeck();
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

    #[Route("/card/deck/draw_more", name: "deck_draw_more")]
    public function drawMore(        
    SessionInterface $session
    ): Response
    {
        if ($session->has("deck")) {
            $deck = $session->get("deck");
        } else {
            $deck = new DeckOfCards();
            $session->set("deck", $deck);
            $session->set("remaining", $deck->size());
        }

        //$deck = new DeckOfCards();
        $deck->shuffleDeck();
        $cards = $deck->drawMany(3);

        $imgPaths = [];
        foreach ($cards as $card) {
            $imgPaths[] = $card->getImgName();
        }


        $data = [
        //     "imgPath" => $card->getImgName(),
            "remaining" => $deck->size(),
        //     "reaction" => $reaction,
        //     "value" => $drawnCardValue,
            "imgPaths" => $imgPaths
        ];

        return $this->render('card/deck/draw_more.html.twig', $data);
    }

    #[Route("/card/deck/draw/{num<\d+>}", name: "draw_many")]
    public function drawMany(int $num, SessionInterface $session): Response
    {

        if ($session->has("deck")) {
            $deck = $session->get("deck");
            // var_dump($deck);
            // echo $session->get("remaining");
            // echo "session is set";

        } else {
            $deck = new DeckOfCards();
            //var_dump($deck);

            $session->set("deck", $deck);
            $session->set("remaining", $deck->size());
            
        }

        $remains = $session->get("remaining");
        // echo $remains;

        if ($num > $remains) {
            //throw new \Exception("Only {$remains} cards in deck!");
            $redirectRoute = $this->generateUrl('empty_deck');
            return new RedirectResponse($redirectRoute);
        }
        
        //$deck = new DeckOfCards();
        $deck->shuffleDeck();
        $cards = $deck->drawMany($num);
        $session->set("remaining", $deck->size());
        $imgPaths = [];
        foreach ($cards as $card) {
            $imgPaths[] = $card->getImgName();
        }


        $data = [
        //     "imgPath" => $card->getImgName(),
            "session" => $session,
        //     "reaction" => $reaction,
        //     "value" => $drawnCardValue,
            "imgPaths" => $imgPaths
        ];

        return $this->render('card/deck/draw_more.html.twig', $data);
    }
}