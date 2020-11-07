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
use App\Model\Castle;
use App\Model\CastleManager;

class GameController extends AbstractController
{
    private $troopManager;
    private $enemyManager;
    private $castleManager;

    public function __construct()
    {
        parent::__construct();
        $this->troopManager = new TroopManager();
        $this->enemyManager = new EnemyManager();
        $this->castleManager = new CastleManager();
    }

    public function init(): string
    {
        if (false === $this->troopManager->deleteAll()) {
            header("HTTP/1.1 503 Service Unavailable");
            echo '503 - Service Unavailable';
        }

        if (false === $this->enemyManager->deleteAttacker()) {
             header("HTTP/1.1 503 Service Unavailable");
             echo '503 - Service Unavailable';
             return "";
        }

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

        foreach ($troops as $troop) {
            if (false === $this->troopManager->insert($troop)) {
                header("HTTP/1.1 503 Service Unavailable");
                echo '503 - Service Unavailable';
            }
        }

        if (false === $this->castleManager->deleteAll()) {
            header("HTTP/1.1 503 Service Unavailable");
            echo '503 - Service Unavailable';
        }

        $castle = new Castle();
        $castle->setName("DEFEND THE CASTLE");
        $castle->setScore();

        if (false === $this->castleManager->insert($castle)) {
            header("HTTP/1.1 503 Service Unavailable");
            echo '503 - Service Unavailable';
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
            $castle = $this->castleManager->selectOneById(1);
            return $this->twig->render("Game/troop.html.twig", ["troops" => $troops, "enemy" => $enemy,
            "castle" => $castle]);
    }
}
