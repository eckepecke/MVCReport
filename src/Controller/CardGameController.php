<?php

namespace App\Controller;
use App\Cards\Card;
use App\Cards\CardGraphic;
use App\Cards\CardHand;
use App\Cards\DeckOfCards;




use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardGameController extends AbstractController
{
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

    #[Route("/card/deck/shuffle", name: "deck")]
    public function shuffledDeck(): Response
    {

        $deck = new DeckOfCards();
        $deck->shuffleDeck();


        $data = [
            "cards" => $deck->getImgNames()
        ];

        return $this->render('card/deck/shuffle.html.twig', $data);
    }
}