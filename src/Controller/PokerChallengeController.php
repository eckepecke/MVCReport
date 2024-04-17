<?php

namespace App\Controller;

use App\Poker\ActionSequence;
use App\Poker\Challenge;
use App\Poker\Hero;
use App\Poker\Table;
use App\Poker\Villain;
use App\Poker\ChallengeDealer;
use App\Poker\ChallengeTable;
use App\Cards\DeckOfCards;



use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PokerChallengeController extends AbstractController
{
    #[Route("/poker/session", name: "session")]
    public function sessionCheck(
        SessionInterface $session
    ): Response {
        $challenge = $session->get("challenge");
        $hero = $session->get("hero");
        $villain = $session->get("villain");
        $table = $session->get("table");
        $dealer = $session->get("dealer");
        $deck = $session->get("deck");
        echo $deck->size();


        $data = [
            "cards_left" => $deck->size()
        ];
        return $this->render('poker/session.html.twig', $data);
    }

    #[Route("/poker/session/delete", name: "session_delete")]
    public function sessionDelete(
        Request $request,
        SessionInterface $session
    ): Response {
        $session->invalidate();
        $this->addFlash(
            'notice',
            'Session data was deleted!'
        );
        return $this->render('poker/delete.html.twig');
    }
    // #[Route("/game", name: "game")]
    //     public function home(): Response
    //     {
    //         $hero = new Hero;
    //         $villain = new Villain;

    //         $challenge = new Challenge(4);

    //         $challenge->addHero($hero);
    //         $challenge->addVillain($villain);

    //         $duration = $challenge->getDuration();

    //         $heroName = $challenge->getHeroName();
    //         $villainName = $challenge->getVillainName();


    //         $data = [
    //             "header" => "Welcome to the pokerchallenge",
    //             "duration" => $duration,
    //             "hero" => $heroName,
    //             "villain" => $villainName
    //         ];

    //         return $this->render('game/game.html.twig', $data);
    //     }

        #[Route("/game", name: "game_init_get", methods: ['GET'])]
        public function init(): Response
        {
            return $this->render('poker/game.html.twig');
        }


    #[Route("/game", name: "game_init_post", methods: ['POST'])]
    public function initCallback(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $handsToPlay = $request->request->get('num_hands');
        $hero = new Hero();
        $villain = new Villain();
        $challenge = new Challenge($handsToPlay);
        $challenge->addHero($hero);
        $challenge->addVillain($villain);
        $table = new ChallengeTable(25, 50);
        $deck = new DeckOfCards();
        $playerList = [$hero, $villain];

        $dealer = new ChallengeDealer($playerList);
        var_dump($dealer);
        $dealer->addDeck($deck);
        $table->seatDealer($dealer);
        $actionSequence = new actionSequence();



        $data = [
            "header" => "Welcome to the pokerchallenge",
            "duration" => $challenge->getDuration(),
            "hero" => $challenge->getHeroName(),
            "villain" => $challenge->getVillainName(),
            "hands_played" => $challenge->getHandsPlayed(),
            "pot_size" => $table->getPotSize(),
            "teddy_hand" => $villain->getHoleCards(),
            "mos_hand" => $hero->getHoleCards()
        ];

        $session->set("challenge", $challenge);
        $session->set("hero", $hero);
        $session->set("villain", $villain);
        $session->set("table", $table);
        $session->set("dealer", $dealer);
        $session->set("deck", $deck);
        $session->set("action_sequence", $actionSequence);


        return $this->redirectToRoute('play');

        //return $this->render('/play.html.twig', $data);


    }

    #[Route("/play", name: "play", methods: ['GET'])]
    public function play(
        SessionInterface $session
    ): Response
    {

        $challenge = $session->get("challenge");
        $hero = $session->get("hero");
        $villain = $session->get("villain");
        $table = $session->get("table");
        $dealer = $session->get("dealer");
        $deck = $session->get("deck");
        $actionSequence = $session->get("action_sequence");

        if ($challenge->challengeComplete()) {
            return $this->render('poker/end_game.html.twig');
        }
        //make the the table look like a table
        //render mos options with buttons that lead back to this route
        //and trigger the correct funtions
        //how do I know when to deal the flop?
        //maybe an action sequence array?
        //$table->getStreet()

        $currentStreet = $table->getStreet();
        $previousAction = $actionSequence->getSequence();
        if ($currentStreet === 1) {
            if ($previousAction === []) {
                //preflop flow
                if ($challenge->getHandsPlayed() === 0) {
                    $dealer->randButton();
                } else {
                    $dealer->moveButton();
                }
                $deck->shuffleDeck();
                $blinds = $dealer->chargeAntes(25, 50);
                if ($villain->getPosition() === "SB") {
                    $villain->setCurrentBet(25);
                    $hero->setCurrentBet(50);
                } else {
                    $villain->setCurrentBet(50);
                    $hero->setCurrentBet(25);
                }

                $priceToPlay = 
                $table->addChipsToPot($blinds);
                $dealer->dealHoleCards();
            }
            if ($previousAction === ["call","check"]) {
                return $this->redirectToRoute('deal_flop');
            }

            if ($previousAction === ["call","raise"]) {

                /// figure out how to get chips on the board and how much mos needs to call
                return $this->redirectToRoute('teddy raised');
            }


        }


        if ($currentStreet === 2) {
            $flop = $dealer->dealFlop();
            var_dump($flop);
            return $this->render('här delar vi floppen', $data);

        }
        $heroBet = $hero->getCurrentBet();
        $villainBet = $villain->getCurrentBet();

        $data = [
            "teddy_hand" => $villain->getImgPaths(),
            "teddy_stack" =>$villain->getStack(),
            "teddy_pos" => $villain->getPosition(),
            "pot_size" => $table->getPotSize(),
            "mos_hand" => $hero->getImgPaths(),
            "mos_pos" => $hero->getPosition(),
            "mos_stack" => $hero->getStack(),
            "teddy_bet" => $villain->getCurrentBet(),
            "mos_bet" => $hero->getCurrentBet(),
            "price" => $dealer->getPriceToPlay($heroBet, $villainBet),
        ];

        //$challenge->incrementHandsPlayed();
        return $this->render('poker/preflop.html.twig', $data);
    }

    #[Route("/game/fold", name: "fold", methods: ['POST'])]
    public function fold(
        SessionInterface $session
    ): Response
    {
        $table = $session->get("table");
        $villain = $session->get("villain");
        $hero = $session->get("hero");
        $challenge = $session->get("challenge");
        $actionSequence = $session->get("action_sequence");


        $pot = $table->getPotSize();
        $villain->takePot($pot);
        $table->resetPotSize();
        $villain->muckCards();
        $hero->muckCards();
        $actionSequence->resetSequence();
        $challenge->incrementHandsPlayed();

        return $this->redirectToRoute('play');

    }

    #[Route("/game/call", name: "call", methods: ['POST'])]
    public function call(
        SessionInterface $session
    ): Response
    {


        $table = $session->get("table");
        $villain = $session->get("villain");
        $hero = $session->get("hero");
        $challenge = $session->get("challenge");
        $actionSequence = $session->get("action_sequence");

        $villain->resetCurrentBet();
        $hero->resetCurrentBet();

        $actionSequence->addAction("call");
        $previousAction = $actionSequence->getSequence();

        if (count($previousAction) < 2) {
            $smallBlind = $table->getSmallBlind();
            $hero->call($smallBlind);
            $action = $villain->decisionFacingLimp();
            $actionSequence->addAction($action);

            if ($action === "check") {
                $table->setNextStreet();
                return $this->redirectToRoute('play');
            }
            if ($action === "raise") {
                $betSize = 4 * ($table->getPotSize());
                $villain->bet($betSize);
                $villain->setCurrentBet($betSize);
            }
        } else {
            $table->getStreet();
            return $this->redirectToRoute('call but more than 2 actions');
        }
        // $hand = $session->get("pig_dicehand");
        // $hand->roll();

        // $roundTotal = $session->get("pig_round");
        // $round = 0;
        // $values = $hand->getValues();
        // foreach ($values as $value) {
        //     if ($value === 1) {
        //         $this->addFlash(
        //             'warning',
        //             'You got a 1 and you lost the round points!'
        //         );
        //         $round = 0;
        //         $roundTotal = 0;
        //         break;
        //     }
        //     $round += $value;
        // }

        // $session->set("pig_round", $roundTotal + $round);
        
        //return $this->redirectToRoute('pig_play');
        return $this->redirectToRoute('play');

    }

    #[Route("/game/bet", name: "bet", methods: ['POST'])]
    public function bet(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $betSize = $request->request->get('bet');
        var_dump($betSize);
        
        // $hand = $session->get("pig_dicehand");
        // $hand->roll();

        // $roundTotal = $session->get("pig_round");
        // $round = 0;
        // $values = $hand->getValues();
        // foreach ($values as $value) {
        //     if ($value === 1) {
        //         $this->addFlash(
        //             'warning',
        //             'You got a 1 and you lost the round points!'
        //         );
        //         $round = 0;
        //         $roundTotal = 0;
        //         break;
        //     }
        //     $round += $value;
        // }

        // $session->set("pig_round", $roundTotal + $round);
        
        //return $this->redirectToRoute('pig_play');
        return $this->render('poker/bet.html.twig');

    }
}
