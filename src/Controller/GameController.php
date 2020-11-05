<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 26/10/20
 * Time: 9:00
 */

namespace App\Controller;

use App\Model\Troop;
use App\Model\TroopManager;
use App\Model\EnemyManager;

class GameController extends AbstractController
{
    private $troopManager;

    private $enemyManager;

    public function __construct()
    {
        parent::__construct();
        $this->troopManager = new TroopManager();
        $this->enemyManager = new EnemyManager();
    }

    public function init(): string
    {
        if (false === $this->troopManager->deleteAll()) {
            header("HTTP/1.0 404 Not Found");
            echo '404 - Page not found';
        }

        if (false === $this->enemyManager->deleteAttacker()) {
            header("HTTP/1.1 503 Service Unavailable");
            echo '503 - Service Unavailable';
            return "";
        }
        
        //-------------------------------------Archer------------------------------------
        $troops[0] = new Troop();
        $troops[0]->setName("Archer");
        $troops[0]->setRandomLevel();
        //-------------------------------------Horseman----------------------------------
        $troops[1] = new Troop();
        $troops[1]->setName("Horseman");
        $troops[1]->setRandomLevel();
        //-------------------------------------Lancer------------------------------------
        $troops[2] = new Troop();
        $troops[2]->setName("Lancer");
        $troops[2]->setRandomLevel();
        shuffle($troops);


        foreach ($troops as $troop) {
            if (false === $this->troopManager->insert($troop)) {
                header("HTTP/1.0 404 Not Found");
                echo '404 - Page not found';
            }
        }
        header('Location: /game/play');
        return "";
    }

    public function play(): string
    {
        $enemy = $this->enemyManager->selectCurrent();

        if (null === $enemy) {
            $enemy = new Troop();
            $enemy->setRandomName();
            $enemy->setRandomLevel();
            $id = $this->enemyManager->insertEnemy($enemy);
            if (EnemyManager::ERROR === $id) {
                header("HTTP/1.1 503 Service Unavailable");
                echo '503 - Service Unavailable';
                return "";
            }
            $enemy->setId($id);
        }
            $troops = $this->troopManager->selectAll();
            return $this->twig->render("Game/troop.html.twig", ["troops" => $troops, "enemy" => $enemy]);
    }
}
