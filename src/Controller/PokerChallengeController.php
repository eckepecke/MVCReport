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

    #[Route("/test", name: "test", methods: ['GET'])]
    public function test(
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
        
        $dealer->moveButton();
        $deck->shuffleDeck();
        //need to turn these in to small blinds
        $blinds = $table->chargeAntes(25, 50);
        $dealer->dealHoleCards();


        $action = "preflopCall";


        if ($table->getSbPlayer() === $villain) {
            /////////////////////////////////////

            ////////////////////////////////////
            // $action = $villain->randActionRFI();
            // var_dump($action);

            if ($action === "preflopRaise"){
                echo "raise";

                $raise = $villain->$action($table->getSmallBlind(), $table->getBigBlind());
                $table->addChipsToPot(($raise - $table->getSmallBlind()));

            } elseif($action === "preflopCall") {
                echo "Call";
                $chipAmount = $table->getPriceToPlay();
                $villain->$action($chipAmount);
                $table->addChipsToPot($chipAmount);

            } else {
                echo "Fold";
                $villain->fold();
                $hero->muckCards();
                $hero->takePot($table->getPotSize());
                $table->resetPotSize();
                $challenge->incrementHandsPlayed();
                $data = $this->getSessionVariables($session);
                return $this->render('poker/teddy_fold.html.twig', $data);
            }
    }
    $data = $this->getSessionVariables($session);
    //are players allin?
    
    return $this->render('poker/test.html.twig', $data);
}
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
        $dealer->addDeck($deck);
        $table->seatDealer($dealer);
        $table->seatPlayers($villain, $hero);

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

        return $this->redirectToRoute('test');
    }

    #[Route("/preflop", name: "preflop", methods: ['GET'])]
    public function play(
        SessionInterface $session
    ): Response
    {
        $debug = true;
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

        //preflop flow
        if ($challenge->challengeComplete()) {
            return $this->render('poker/end_game.html.twig');
        }
        
        $dealer->moveButton();
        $deck->shuffleDeck();
        //need to turn these in to small blinds
        $blinds = $table->chargeAntes(25, 50);
        $dealer->dealHoleCards();

        if ($table->getSbPlayer() === $villain) {
            /////////////////////////////////////

            ////////////////////////////////////
            $action = $villain->randActionRFI();
            var_dump($action);

            if ($action === "preflopRaise"){
                echo "raise";

                $raise = $villain->$action($table->getSmallBlind(), $table->getBigBlind());
                $table->addChipsToPot(($raise - $table->getSmallBlind()));

            } elseif($action === "preflopCall") {
                echo "Call";
                $chipAmount = $table->getPriceToPlay();
                $villain->$action($chipAmount);
                $table->addChipsToPot($chipAmount);

            } else {
                echo "Fold";
                $villain->fold();
                $hero->muckCards();
                $hero->takePot($table->getPotSize());
                $table->resetPotSize();
                $challenge->incrementHandsPlayed();
                $data = $this->getSessionVariables($session);
                return $this->render('poker/teddy_fold.html.twig', $data);
            }
    }
    $data = $this->getSessionVariables($session);
    //are players allin?
    
    return $this->render('poker/test.html.twig', $data);
}


    // #[Route("/flop", name: "flop", methods: ['GET'])]
    // public function flop(
    //     SessionInterface $session
    // ): Response
    // {
    //     var_dump($this->challenge);
    //     $sessionVars = $this->getSessionVariables($session);

    //     $street = 2;
    //     $challenge = $session->get("challenge");
    //     $hero = $session->get("hero");
    //     $villain = $session->get("villain");
    //     $table = $session->get("table");
    //     $dealer = $session->get("dealer");
    //     $deck = $session->get("deck");
    //     $actionSequence = $session->get("action_sequence");

    //     // if flopaction = []{
    //     //     $flop = $dealer->dealFlop();

    //     // }


    //     $heroBet = $hero->getCurrentBet();
    //     $villainBet = $villain->getCurrentBet();

    //     $data = [
    //         "teddy_hand" => $villain->getImgPaths(),
    //         "teddy_stack" =>$villain->getStack(),
    //         "teddy_pos" => $villain->getPosition(),
    //         "pot_size" => $table->getPotSize(),
    //         "mos_hand" => $hero->getImgPaths(),
    //         "mos_pos" => $hero->getPosition(),
    //         "mos_stack" => $hero->getStack(),
    //         "teddy_bet" => $villain->getCurrentBet(),
    //         "mos_bet" => $hero->getCurrentBet(),
    //         "price" => $dealer->getPriceToPlay($heroBet, $villainBet),
    //     ];
    // }

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
        $actionSequence = $session->get("action_sequence");

        $street = $table->getStreet();
        $actionSequence->addPreflopAction($street, "call");
        $previousAction = $actionSequence->getCurrentStreetAction($street);

        if ($previousAction === ["call"] && $street === 1) {
            return $this->redirectToRoute('limp');
        }

        if(count($previousAction === ["raise", "raise", "call"] && $street === 1)) {
            return $this->redirectToRoute('villain_preflop_raise');
        }

        if(count($previousAction === 3 && $street >= 2)) {
            return $this->redirectToRoute('villain_postflop_raise');
        }

        return $this->redirectToRoute('preflop');

    }



    #[Route("/game/bet", name: "bet", methods: ['POST'])]
    public function bet(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $betSize = $request->request->get('bet');
        var_dump($betSize);

        return $this->render('poker/bet.html.twig');

    }

    #[Route("/game/check", name: "check", methods: ['POST'])]
    public function check(
        Request $request,
        SessionInterface $session
    ): Response
    {




        $table = $session->get("table");
        $dealer = $session->get("dealer");
        $villain = $session->get("villain");
        $hero = $session->get("hero");

        $street = $table->getStreet();
        $heroPos = $hero->getPosition();

        if ($street === 4) {
            $table->incrementStreet();
            return $this->render('poker/showdown.html.twig');
        }

        if ($heroPos === "SB" || ($heroPos === "BB" && $street === 1)) {
            $table->incrementStreet();
        }

        $street = $table->getStreet();

        if ($street === 2) {
            echo "hello 2";
            $flop = $dealer->dealFlop();
            $table->registerFlop($flop);
        }

        if ($street === 3) {
            echo "hello 3";
            $turn = $dealer->dealOne();
            $table->registerTurn($turn);
        }

        if ($street === 4) {
            echo "hello 4";
            $river = $dealer->dealOne();
            $table->registerRiver($river);
        }

        if ($villain->getPosition() === "BB"){
            // $action = $villain->actionVsCheck();
            $action = "bet";
            if ($action === "check") {
                $table->incrementStreet();
            } 
            if ($action === "bet") {
                echo "betting";
                $betSize = $villain->betVsCheck($table->getPotSize());
                $villain->bet($betSize);
            } 
        }

        $data = $this->getSessionVariables($session);
        // var_dump($table->getStreet());
        // var_dump($table->getBoard());


        return $this->render('poker/test.html.twig', $data);
    }

    private function getSessionVariables(SessionInterface $session): array
    {


        $challenge = $session->get("challenge");
        $hero = $session->get("hero");
        $villain = $session->get("villain");
        $table = $session->get("table");
        $dealer = $session->get("dealer");
        $deck = $session->get("deck");
        $actionSequence = $session->get("action_sequence");

        $board = $table->getCardImages();

        return [
            "teddy_hand" => $villain->getImgPaths(),
            "teddy_stack" =>$villain->getStack(),
            "teddy_pos" => $villain->getPosition(),
            "pot_size" => $table->getPotSize(),
            "mos_hand" => $hero->getImgPaths(),
            "mos_pos" => $hero->getPosition(),
            "mos_stack" => $hero->getStack(),
            "teddy_bet" => $villain->getCurrentBet(),
            "mos_bet" => $hero->getCurrentBet(),
            "price" => $table->getPriceToPlay(),
            "min_raise" => $table->getMinimumRaiseAllowed(),
            "board" => $board,
        ];
    }
    
}
