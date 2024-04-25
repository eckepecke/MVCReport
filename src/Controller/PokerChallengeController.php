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
use App\Cards\TexasCardHand;

use App\Poker\HandChecker;


use App\Cards\CardHand;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PokerChallengeController extends AbstractController
{
    #[Route("/preflop", name: "preflop", methods: ['GET'])]
    public function test(
        SessionInterface $session
    ): Response {
        $challenge = $session->get("challenge");
        $hero = $session->get("hero");
        $villain = $session->get("villain");
        $table = $session->get("table");
        $dealer = $session->get("dealer");
        $deck = $session->get("deck");

        if ($challenge->challengeComplete()) {
            $startStack = $session->get("hero_start_stack");
            $data = $this->getSessionVariables($session);
            $data["result"] = $challenge->getResult($startStack, $hero->getStack());
            return $this->render('poker/end_game.html.twig', $data);
        }

        $table->moveButton();
        $table->moveButton();

        $deck->initializeCards();
        $deck->shuffleDeck();
        //need to turn these in to small blinds
        $table->chargeAntes(25, 50);
        $dealer->dealHoleCards();

        if ($table->getSbPlayer() === $villain) {
            $action = $villain->randActionRFI();
            var_dump($action);
            switch ($action) {
                case "preflopRaise":
                    echo "raise";
                    $heroBet = $hero->getCurrentBet();
                    $villain->raise($heroBet);
                    break;

                case "preflopCall":
                    echo "Call";
                    $chipAmount = $table->getPriceToPlay();
                    $villain->$action($chipAmount);
                    break;

                default:
                    echo "Fold";
                    $villain->fold();
                    $hero->muckCards();
                    var_dump($table->getPotSize());
                    $hero->takePot($table->getBlinds());
                    $table->cleanTable();
                    $challenge->incrementHandsPlayed();
                    $data = $this->getSessionVariables($session);
                    return $this->render('poker/teddy_fold.html.twig', $data);
        }
        $data = $this->getSessionVariables($session);
        return $this->render('poker/test.html.twig', $data);
    }
}


    #[Route("/poker/session/delete", name: "session_delete")]
    public function sessionDelete(
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
    ): Response {
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

        $challenge->addDealer($dealer);
        $challenge->addTable($table);
        $heroStartStack = $hero->getStack();

        $session->set("challenge", $challenge);
        $session->set("hero", $hero);
        $session->set("villain", $villain);
        $session->set("table", $table);
        $session->set("dealer", $dealer);
        $session->set("deck", $deck);
        $session->set("hero_start_stack", $heroStartStack);

        return $this->redirectToRoute('preflop');
    }

    #[Route("/game/fold", name: "fold", methods: ['GET', 'POST'])]
    public function fold(
        SessionInterface $session
    ): Response {
        $table = $session->get("table");
        $villain = $session->get("villain");
        $hero = $session->get("hero");
        $challenge = $session->get("challenge");

        $villainBet = $villain->getCurrentBet();
        $heroBet = $hero->getCurrentBet();
        $table->addChipsToPot($villainBet);
        $table->addChipsToPot($heroBet);
        $pot = $table->getPotSize();

        $winner = $hero;
        $biggestBet = max($villainBet, $heroBet);
        if ($biggestBet === $villainBet) {
            $winner = $villain;
        }
        $winner->takePot($pot);

        $hero->fold();
        $villain->fold();
        $table->cleanTable();
        $challenge->incrementHandsPlayed();

        return $this->redirectToRoute('preflop');
    }

    #[Route("/game/call", name: "call", methods: ['GET', 'POST'])]
    public function call(
        SessionInterface $session
    ): Response {
        $table = $session->get("table");
        $dealer = $session->get("dealer");
        $villain = $session->get("villain");
        $hero = $session->get("hero");

        $villainBet = $villain->getCurrentBet();
        $heroBet = $hero->getCurrentBet();
        $biggestBet = max($villainBet, $heroBet);
        $price = $table->getPriceToPlay();
        $caller = $hero;

        if ($biggestBet === $heroBet) {
            $caller = $villain;
        }
        $caller->call($price);

        $table->addChipsToPot($heroBet);
        $table->addChipsToPot($villainBet);
        $table->addChipsToPot($price);

        $villain->resetCurrentBet();
        $hero->resetCurrentBet();

        if($dealer->playersAllIn()) {
            $board = $table->getBoard();
            $cards = $dealer->dealRemaining($board);
            $table->registerMany($cards);

            return $this->redirectToRoute('showdown');
        }

        $street = $table->getStreet();

        if ($street === 1) {
            $flop = $dealer->dealFlop();
            $table->registerMany($flop);
            $table->incrementStreet();

            $data = $this->getSessionVariables($session);
            return $this->render('poker/test.html.twig', $data);
        }

        if ($street === 2) {
            $turn = $dealer->dealOne();
            $table->registerOne($turn);
            $table->incrementStreet();

            $data = $this->getSessionVariables($session);
            return $this->render('poker/test.html.twig', $data);
        }

        if ($street === 3) {
            $river = $dealer->dealOne();
            $table->registerOne($river);
            $table->incrementStreet();

            $data = $this->getSessionVariables($session);
            return $this->render('poker/test.html.twig', $data);
        }

        if ($street === 4) {
            return $this->redirectToRoute('showdown');
        }

        return $this->redirectToRoute('no streets');
    }

    #[Route("/game/bet", name: "bet", methods: ['POST'])]
    public function bet(
        Request $request,
        SessionInterface $session
    ): Response {
        $heroBet = $request->request->get('bet');
        $villain = $session->get("villain");
        $hero = $session->get("hero");

        $hero->bet($heroBet);
        $action = $villain->actionFacingBet();

        if($action === "fold") {
            return $this->redirectToRoute('fold');
        }

        if($action === "call") {
            return $this->redirectToRoute('call');
        }

        if($heroBet > $villain->getStack() || $hero->getStack() <= 0) {
            return $this->redirectToRoute('call');
        }
        $villain->raise($heroBet);

        $data = $this->getSessionVariables($session);
        return $this->render('poker/test.html.twig', $data);
    }

    #[Route("/game/check", name: "check", methods: ['POST'])]
    public function check(
        SessionInterface $session
    ): Response {
        $table = $session->get("table");
        $dealer = $session->get("dealer");
        $villain = $session->get("villain");
        $hero = $session->get("hero");
        $challenge = $session->get("challenge");


        $heroPos = $hero->getPosition();
        $street = $table->getStreet();
        if (($heroPos === "BB" && $street === 1 && $table->getFlop() === [] )) {
            //Adding chips when hero checks back preflop
            $table->collectUnraisedPot();
        }

        $table->dealCorrectStreet($heroPos, $street);

        if ($street === 1) {
            // we reach this when river has already been dealt
            return $this->redirectToRoute('showdown');
        }

        if ($villain->getPosition() === "SB") {
            $action = $villain->actionVsCheck();
            if ($action === "check") {
                if ($street >= 4) {
                    return $this->redirectToRoute('showdown');
                }
                $card = $dealer->dealOne();
                $table->registerOne($card);
                $table->incrementStreet();
            }
            if ($action === "bet") {
                $betSize = $villain->betVsCheck($table->getPotSize());
                $villain->bet($betSize);
            }
        }

        $data = $this->getSessionVariables($session);
        return $this->render('poker/test.html.twig', $data);
    }

    #[Route("/game/showdown", name: "showdown", methods: ['GET', 'POST'])]
    public function showdown(
        SessionInterface $session
    ): Response {
        $table = $session->get("table");

        $villain = $session->get("villain");
        $hero = $session->get("hero");
        $challenge = $session->get("challenge");

        $handChecker = new HandChecker();
        $board = $table->getBoard();

        $fullHeroHand = array_merge($hero->getHoleCards(), $board);
        $heroStrength = $handChecker->evaluateHand($fullHeroHand);
        $hero->updateStrength($heroStrength);

        $handChecker->resetStrengthArray();

        $fullVillainHand = array_merge($villain->getHoleCards(), $board);
        $villainStrength = $handChecker->evaluateHand($fullVillainHand);
        $villain->updateStrength($villainStrength);

        $winner = $handChecker->compareStrength($hero, $villain);
        $winner->takePot($table->getPotsize());

        $data = $this->getSessionVariables($session);
        $data["teddy_hand_strength"] = $villain->getStrength();
        $data["mos_hand_strength"] = $hero->getStrength();
        $data["winner"] = $winner->getName();
        $hero->fold();
        $villain->fold();
        $table->cleanTable();
        $challenge->incrementHandsPlayed();
        return $this->render('poker/showdown.html.twig', $data);
    }

    private function getSessionVariables(SessionInterface $session): array
    {
        $hero = $session->get("hero");
        $villain = $session->get("villain");
        $table = $session->get("table");
        $board = $table->getCardImages();

        return [
            "teddy_hand" => $villain->getImgPaths(),
            "mos_hand" => $hero->getImgPaths(),
            "teddy_stack" => $villain->getStack(),
            "mos_stack" => $hero->getStack(),
            "teddy_pos" => $villain->getPosition(),
            "mos_pos" => $hero->getPosition(),
            "pot_size" => $table->getPotSize(),
            "teddy_bet" => $villain->getCurrentBet(),
            "mos_bet" => $hero->getCurrentBet(),
            "price" => $table->getPriceToPlay(),
            "min_raise" => $table->getMinimumRaiseAllowed(),
            "board" => $board,
            "street" => $table->getStreet(),
        ];
    }

    #[Route("/api/game", name: "api_game", methods: ["POST", "GET"])]
    public function apiPoker(
        SessionInterface $session
    ): Response {
        if (!$session->has("challenge")) {
            throw new \Exception("No deck in session!");
        }

        $data = $this->getSessionVariables($session);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
