<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends AbstractController
{
    #[Route('/lucky/card', name: 'lucky_card')]
    public function card(): Response
    {
        $cards = array(
            'diamonds_' => array(
                'ace', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'jack', 'queen', 'king'
            ),
            'hearts_' => array(
                'ace', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'jack', 'queen', 'king'
            ),
            'clubs_' => array(
                'ace', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'jack', 'queen', 'king'
            ),
            'spades_' => array(
                'ace', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'jack', 'queen', 'king'
            ),
        );

        $suits = array_keys($cards);
        $randomSuit1 = $suits[array_rand($suits)];
        $randomSuit2 = $suits[array_rand($suits)];

        $randomCardValue1 = $cards[$randomSuit1][array_rand($cards[$randomSuit1])];
        $randomCardValue2 = $cards[$randomSuit2][array_rand($cards[$randomSuit2])];

        $imgpath1 = "$randomSuit1$randomCardValue1.svg";
        $imgpath2 = "$randomSuit2$randomCardValue2.svg";

        $imgpath3 = "bad_hand.png";

        if ($randomCardValue1 == $randomCardValue2 || $randomSuit1 == $randomSuit2 || $randomCardValue1 == 'ace' || $randomCardValue2 == 'ace') {
            $imgpath3 = "good_hand.png";
        }

        return $this->render('lucky/card.html.twig', [
            'img_path1' => $imgpath1,
            'img_path2' => $imgpath2,
            'img_path3' => $imgpath3
        ]);
    }
}
