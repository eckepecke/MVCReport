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
        
        $table->moveButton();
        $table->moveButton();

        $deck->shuffleDeck();
        //need to turn these in to small blinds
        $blinds = $table->chargeAntes(25, 50);
        $dealer->dealHoleCards();




        $action = "preflopRaise";



        if ($table->getSbPlayer() === $villain) {
            /////////////////////////////////////

            ////////////////////////////////////
            // $action = $villain->randActionRFI();
            // var_dump($action);

            if ($action === "preflopRaise"){
                echo "raise";
                $heroBet = $hero->getCurrentBet();


                $raise = $villain->raise($heroBet);
                //$table->addChipsToPot(($raise - $currentBet));

            } elseif($action === "preflopCall") {
                echo "Call";
                $chipAmount = $table->getPriceToPlay();
                $villain->$action($chipAmount);
                $table->addChipsToPot($chipAmount);
                $villain->resetCurrentBet();
                $hero->resetCurrentBet();


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
        
        $table->moveButton();
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
                //$table->addChipsToPot(($raise - $table->getSmallBlind()));

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

    #[Route("/game/fold", name: "fold", methods: ['GET', 'POST'])]
    public function fold(
        SessionInterface $session
    ): Response
    {
        $table = $session->get("table");
        $villain = $session->get("villain");
        $hero = $session->get("hero");
        $challenge = $session->get("challenge");

        $villainBet = $villain->getCurrentBet();
        $heroBet = $hero->getCurrentBet();

        $table->addChipsToPot($villainBet);
        $table->addChipsToPot($heroBet);
        $pot = $table->getPotSize();


        $biggestBet = max($villainBet, $heroBet);
        if ($biggestBet === $villainBet) {
            $villain->takePot($pot);
        } else {
            $hero->takePot($pot);
        }
        $hero->fold();
        $villain->fold();
        $table->resetPotSize();
        $table->cleanTable();
        $challenge->incrementHandsPlayed();

        return $this->redirectToRoute('test');
    }

    #[Route("/game/call", name: "call", methods: ['GET', 'POST'])]
    public function call(
        SessionInterface $session
    ): Response
    {
        $table = $session->get("table");
        $dealer = $session->get("dealer");
        $villain = $session->get("villain");
        $hero = $session->get("hero");


        $villainBet = $villain->getCurrentBet();
        $heroBet = $hero->getCurrentBet();

        $biggestBet = max($villainBet, $heroBet);

        echo $villainBet;
        $price = $table->getPriceToPlay();
        echo $price;

        if ($biggestBet === $villainBet) {
            $hero->call($price);
        } else {
            $villain->call($price);
        }



        $table->addChipsToPot($heroBet);
        $table->addChipsToPot($villainBet);
        $table->addChipsToPot($price);

        $villain->resetCurrentBet();
        $hero->resetCurrentBet();

        if($dealer->playersAllIn()) {
            $board = $table->getBoard();
            $cards = $dealer->dealRemaining($board);
            $table->registerMany($cards);

            $data = $this->getSessionVariables($session);
            //this should be showdoen route later
            return $this->render('poker/test.html.twig', $data);
        }

        $street = $table->getStreet();




        if ($street === 1) {
            echo "hero preflop call v r";
            
            $flop = $dealer->dealFlop();
            $table->registerMany($flop);
            $table->incrementStreet();
            var_dump($table->getStreet());

            $data = $this->getSessionVariables($session);
            return $this->render('poker/test.html.twig', $data);
        }

        if ($street === 2) {
            echo "hero flop call v b";
            $turn = $dealer->dealOne();
            $table->registerOne($turn);
            $table->incrementStreet();

            $data = $this->getSessionVariables($session);
            return $this->render('poker/test.html.twig', $data);
        }

        if ($street === 3) {
            echo "hero turn call v b";
            $river = $dealer->dealOne();
            $table->registerOne($river);
            $table->incrementStreet();

            $data = $this->getSessionVariables($session);
            return $this->render('poker/test.html.twig', $data);
        }

        if ($street === 4) {
            echo "hero river call v b";

            return $this->redirectToRoute('showdown');

        }

        return $this->redirectToRoute('no streets');

    }



    #[Route("/game/bet", name: "bet", methods: ['POST'])]
    public function bet(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $heroBet = $request->request->get('bet');
        var_dump($heroBet);
        $table = $session->get("table");
        $dealer = $session->get("dealer");
        $villain = $session->get("villain");
        $hero = $session->get("hero");
        var_dump($hero->getCurrentBet());

        //$hero->resetCurrentBet();
        var_dump($hero->getCurrentBet());
        //echo $whut;

        $hero->bet($heroBet);

        $action = $villain->actionFacingBet();
        $action = "raise";
///debug
        if($action === "fold") {
            return $this->redirectToRoute('fold');
        }

        if($action === "call") {
            return $this->redirectToRoute('call');
        }

        if($heroBet > $villain->getStack()) {
            echo "teddy calling";
            //$villain->call($heroBet);
            return $this->redirectToRoute('call');
        } else {
            echo "teddy raising";
            $villain->raise($heroBet);
        }

        $data = $this->getSessionVariables($session);
        return $this->render('poker/test.html.twig', $data);
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

        $hero->resetCurrentBet();
        $villain->resetCurrentBet();
        //var_dump($table->getStreet());

        $street = $table->getStreet();
        $heroPos = $hero->getPosition();

        if ($heroPos === "SB" || ($heroPos === "BB" && $street === 1)) {
            $table->incrementStreet();
            echo "incrementing";
        }

        $street = $table->getStreet();

        if ($street === 2 && ($table->getFlop() === [])) {
            $flop = $dealer->dealFlop();
            $table->registerFlop($flop);
        }

        if ($street === 3 && ($table->getTurn() === [])) {
            $turn = $dealer->dealOne();
            $table->registerOne($turn);
        }

        if ($street === 4 && ($table->getRiver() === [])) {
            $river = $dealer->dealOne();
            $table->registerOne($river);
        }

        if ($street === 4 && $heroPos === "SB") {
            $table->incrementStreet();

            return $this->render('poker/showdown.html.twig');
        }

        if ($villain->getPosition() === "SB"){
            $action = $villain->actionVsCheck();
            $action = "check";
            if ($action === "check") {
                echo "checking";
                $card = $dealer->dealOne();
                $table->registerOne($card);

                $table->incrementStreet();
                //var_dump($street);
            } 
            if ($action === "bet") {
                $betSize = $villain->betVsCheck($table->getPotSize());
                $villain->bet($betSize);
                //$table->addChipsToPot($betSize);
            } 
        }

        $data = $this->getSessionVariables($session);
        var_dump($table->getStreet());
        // var_dump($table->getBoard());
        //var_dump($street);

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
