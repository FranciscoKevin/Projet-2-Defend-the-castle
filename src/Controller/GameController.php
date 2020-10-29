<?php
/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\Troop;
use App\Model\TroopManager;
use App\Model\CastleManager;
use App\Model\Castle;

class GameController extends AbstractController
{
    public function delete()
    {
        $itemManager = new TroopManager();
        $itemManager->delete();
        header('Location:/game/init');
    }

    public function init(): string
    {
        $manager = new TroopManager();
        $archer = new Troop();
        $archer->setName("Archer");
        $archer->setRandomLevel();
        $manager->insert($archer);
        //-------------------------------------Horseman------------------------------------
        $horseman = new Troop();
        $horseman->setName("Horseman");
        $horseman->setRandomLevel();
        $manager->insert($horseman);
        //-------------------------------------Lancer------------------------------------
        $lancer = new Troop();
        $lancer->setName("Lancer");
        $lancer->setRandomLevel();
        $manager->insert($lancer);

        $random = [$archer, $horseman, $lancer];

        return $this->twig->render("Game/init.html.twig", ["troop" => $random]);
    }
}
