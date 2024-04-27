<?php

namespace App\Controller;

use App\FlopAndGo\Dealer;
use App\FlopAndGo\Game;
use App\FlopAndGo\HandChecker;
use App\FlopAndGo\Hero;
use App\FlopAndGo\Moderator;
use App\FlopAndGo\Table;
use App\FlopAndGo\Villain;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GambleController extends AbstractController
{
    #[Route("/game/gamble/init", name: "gamble_init_post", methods: ['POST'])]
    public function initCallback(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $numDice = $request->request->get('num_hands');

        $game = new Game();
        $hero = new Hero();
        $villain = new Villain();
        $table = new Table();
        $dealer = new Dealer();
        $handChecker = new HandChecker();
        $challenge = new Challenge();

        $game->addHero($hero);
        $game->addVillain($villain);
        $game->addTable($table);
        $game->addDealer($Dealer);
        $game->addHero($HandChecker);
        $challenge->addHero($challenge);

        $session->set("game", $game);

        return $this->redirectToRoute('gamble_play');
    }


    #[Route("/game/gamble/play", name: "gamble_play", methods: ['GET'])]
    public function play(
        SessionInterface $session
    ): Response
    {
        $dicehand = $session->get("pig_dicehand");

        $data = [
            "pigDices" => $session->get("pig_dices"),
            "pigRound" => $session->get("pig_round"),
            "pigTotal" => $session->get("pig_total"),
            "diceValues" => $dicehand->getString() 
        ];

        return $this->render('pig/play.html.twig', $data);
    }

    public function getSessionVariables(): array
    {
        $hero = $this->hero;
        $villain = $this->villain;
        $table = $this->table;

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
            "board" => $table->getCardImages(),
            "street" => $table->getStreet(),
            // "teddy_last_action" => $villain->getLastAction(),
            // "winner" => $this->challenge->getHandWinner(),
            // "teddy_hand_strength" => $villain->getStrength(),
            // "mos_hand_strength" => $hero->getStrength(),
        ];
    }
}