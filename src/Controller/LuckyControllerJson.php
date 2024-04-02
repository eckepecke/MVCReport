<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LuckyControllerJson
{
    #[Route("/api/lucky/number")]
    public function jsonNumber(): Response
    {
        $number = random_int(0, 100);

        $data = [
            'lucky-number' => $number,
            'lucky-message' => 'Hi there!',
        ];

        // return new JsonResponse($data);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/quotes")]
    public function jsonQuote(): Response
    {
        $quotes = [
            [
                "quote" => "The happiness of your life depends upon the quality of your thoughts.",
                "author" => "Marcus Aurelius"
            ],
            [
                "quote" => "The soul becomes dyed with the color of its thoughts.",
                "author" => "Marcus Aurelius"
            ],
            [
                "quote" => "You have power over your mind - not outside events. Realize this, and you will find strength.",
                "author" => "Epictetus"
            ],
            [
                "quote" => "The best revenge is not to be like your enemy.",
                "author" => "Epictetus"
            ],
            [
                "quote" => "It is not death that a man should fear, but he should fear never beginning to live.",
                "author" => "Seneca"
            ],
            [
                "quote" => "Waste no more time arguing about what a good man should be. Be one.",
                "author" => "Seneca"
            ],
            [
                "quote" => "The impediment to action advances action. What stands in the way becomes the way.",
                "author" => "Seneca"
            ],
            [
                "quote" => "Wealth consists not in having great possessions, but in having few wants.",
                "author" => "Marcus Aurelius"
            ],
            [
                "quote" => "He is a wise man who does not grieve for the things which he has not, but rejoices for those which he has.",
                "author" => "Marcus Aurelius"
            ],
            [
                "quote" => "He who laughs at himself never runs out of things to laugh at.",
                "author" => "Epictetus"
            ]
        ];
        
        
        $number = random_int(0, 9);

        $data = [
            'rand-index' => $number,
            'quote-of-day' => [
                "quote" => $quotes[$number]["quote"],
                "author" => $quotes[$number]["author"]
            ],
            'created' => date('Y-m-d H:i:s')
        ];

        // // return new JsonResponse($data);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
