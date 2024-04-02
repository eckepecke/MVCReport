<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends AbstractController
{
    #[Route('/lucky/number')]
    public function number(): Response
    {
        $number = random_int(0, 100);

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }

    #[Route("/lucky/hi")]
    public function hi(): Response
    {
        return new Response(
            '<html><body>Hi to you!</body></html>'
        );
    }

    #[Route('/lucky/card', name: 'lucky_card')]
    public function card(): Response
    {
        $cards = array(
            'D' => array(
                'A', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'J', 'Q', 'K'
            ),
            'H' => array(
                'A', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'J', 'Q', 'K'
            ),
            'C' => array(
                'A', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'J', 'Q', 'K'
            ),
            'S' => array(
                'A', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'J', 'Q', 'K'
            ),
        );

        $suits = array_keys($cards);
        $randomSuit1 = $suits[array_rand($suits)];
        $randomSuit2 = $suits[array_rand($suits)];

        $randomCardValue1 = $cards[$randomSuit1][array_rand($cards[$randomSuit1])];
        $randomCardValue2 = $cards[$randomSuit2][array_rand($cards[$randomSuit2])];

        $imgpath1 = "$randomCardValue1$randomSuit1.jpg";
        $imgpath2 = "$randomCardValue2$randomSuit2.jpg";

        $imgpath3 = null; // Initialize $imgpath3
        if ($randomCardValue1 == $randomCardValue2 || $randomSuit1 == $randomSuit2 || $randomCardValue1 == 'A' || $randomCardValue2 == 'A') {
            $imgpath3 = "good_hand.png";
        } else {
            $imgpath3 = "bad_hand.png";
        }

        return $this->render('card.html.twig', [
            'img_path1' => $imgpath1,
            'img_path2' => $imgpath2,
            'img_path3' => $imgpath3
        ]);
    }
}
