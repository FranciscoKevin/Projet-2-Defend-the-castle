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
    private $castleManager;

    private $enemyManager;

    public function __construct()
    {
        parent::__construct();
        $this->troopManager = new TroopManager();
        $this->enemyManager = new EnemyManager();
    }

    public function init(): string
    {
        // Check if access to the database and data deletion
        if (false === $this->troopManager->deleteAll()) {
             header("HTTP/1.1 503 Service Unavailable");
             echo '503 - Service Unavailable';
             return "";
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
        $troops[1] = new Troop();
        $troops[1]->setName("Horseman");
        $troops[1]->setRandomLevel();
        $troops[2] = new Troop();
        $troops[2]->setName("Lancer");
        $troops[2]->setRandomLevel();
        shuffle($troops);

        // Insertion of troops in the database
        foreach ($troops as $troop) {
            //Check if access to the database
            if (false === $this->troopManager->insert($troop)) {
                header("HTTP/1.1 503 Service Unavailable");
                echo '503 - Service Unavailable';
                return "";
            }
        }
        // Check if access to the database and data deletion
        if (false === $this->castleManager->deleteAll()) {
            header("HTTP/1.1 503 Service Unavailable");
            echo '503 - Service Unavailable';
        }
        // Creation of castle
        $castle = new Castle();
        $castle->setScore();
        if (isset($_POST["castle"])) {
            $castle->setName($_POST["castle"]);
        } else {
            $castle->setName("Defend the Castle");
        }
        // Insertion of castle in the database
        if (false === $this->castleManager->insert($castle)) {
            header("HTTP/1.1 503 Service Unavailable");
            echo '503 - Service Unavailable';
        }
        // Redirection after initialization
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
