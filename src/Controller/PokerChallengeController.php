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

        return $this->redirectToRoute('preflop');
    }

    #[Route("/preflop", name: "preflop", methods: ['GET'])]
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

        $previousAction = $actionSequence->getPreflopSequence();
        var_dump($previousAction);

        if ($previousAction === []) {
            //preflop flow
            if ($challenge->getHandsPlayed() === 0) {
                $dealer->randButton();
            } else {
                $dealer->moveButton();
            }
            $deck->shuffleDeck();
            //need to turn these in to small blinds
            $blinds = $dealer->chargeAntes(25, 50);
            if ($villain->getPosition() === "SB") {
                $villain->setCurrentBet(25);
                $hero->setCurrentBet(50);
            } else {
                $villain->setCurrentBet(50);
                $hero->setCurrentBet(25);
            }

            $table->addChipsToPot($blinds);
            $dealer->dealHoleCards();
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

        if ($previousAction === ["call","check"]) {
            $table->setStreet(2);
            return $this->redirectToRoute('flop');
        }

        return $this->render('poker/test.html.twig', $data);
    }

    #[Route("/flop", name: "flop", methods: ['GET'])]
    public function flop(
        SessionInterface $session
    ): Response
    {
        $street = 2;
        $challenge = $session->get("challenge");
        $hero = $session->get("hero");
        $villain = $session->get("villain");
        $table = $session->get("table");
        $dealer = $session->get("dealer");
        $deck = $session->get("deck");
        $actionSequence = $session->get("action_sequence");

        // if flopaction = []{
        //     $flop = $dealer->dealFlop();

        // }


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
        $actionSequence = $session->get("action_sequence");

        $street = $table->getStreet();
        $actionSequence->addPreflopAction($street, "call");
        $previousAction = $actionSequence->getCurrentStreetAction($street);

        if (count($previousAction) < 2 && $street === 1) {
            return $this->redirectToRoute('limp');

        } else {
            $table->getStreet();
            return $this->redirectToRoute('call but more than 1 action');
        }

        return $this->redirectToRoute('preflop');

    }

    #[Route("/game/call_limp", name: "limp", methods: ['GET'])]
    public function buttonLimp(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $street = 1;
        $table = $session->get("table");
        $villain = $session->get("villain");
        $hero = $session->get("hero");
        $challenge = $session->get("challenge");
        $actionSequence = $session->get("action_sequence");

        $villain->resetCurrentBet();
        $hero->resetCurrentBet();
        $table->setStreet($street);

        $hero->call($table->getSmallBlind());
        $action = $villain->decisionFacingLimp();
        $actionSequence->addAction($street, $action);

        if ($action === "check") {
            return $this->redirectToRoute('flop');
        }

        $betSize = 4 * ($table->getPotSize());
        $villain->bet($betSize);
        $villain->setCurrentBet($betSize);
        $table->addChipsToPot($betSize);
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
        $betSize = $request->request->get('bet');
        var_dump($betSize);

        return $this->render('poker/check.html.twig');
    }
}
