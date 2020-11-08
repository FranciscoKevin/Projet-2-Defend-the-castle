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

/**
 * This class is used to control the progress of the game.
 * Defensive and attacking troops are instantiated here as well as the castle.
 * It also generates the different views of the game.
 */
class GameController extends AbstractController
{
    private $troopManager;
    private $castleManager;
    private $enemyManager;

    /**
     * This method adds Managers classes to the constructor of the class inherited from the parent class.
     */
    public function __construct()
    {
        parent::__construct();
        $this->troopManager = new TroopManager();
        $this->enemyManager = new EnemyManager();
        $this->castleManager = new CastleManager();
    }

    /**
     * This method initialize the game by creating the defensive troops and the castle.
     * The properties of the troops and the castle are recorded in their respective databases.
     * This method returns a string and does a redirect.
     */
    public function init(): string
    {
        if (false === $this->troopManager->deleteAll()) {
            header("HTTP/1.1 503 Service Unavailable");
            return $this->twig->render("Error/503.html.twig");
        }

        if (false === $this->enemyManager->deleteAttacker()) {
            header("HTTP/1.1 503 Service Unavailable");
            return $this->twig->render("Error/503.html.twig");
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
                return $this->twig->render("Error/503.html.twig");
            }
        }

        $this->castleManager->truncate();
        $castle = new Castle();
        $castle->resetScore();
        if (isset($_POST["castle"])) {
            $castle->setName($_POST["castle"]);
        } else {
            $castle->setName("Defend the Castle");
        }
        if (false === $this->castleManager->insert($castle)) {
            header("HTTP/1.1 503 Service Unavailable");
            return $this->twig->render("Error/503.html.twig");
        }

        header('Location: /game/play');
        return "";
    }

    /**
     * This method retrieves data from the defensive troops and the castle.
     * She creates a random attacker with a random level.
     * It sends data necessary for the view.
     */
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
                return $this->twig->render("Error/503.html.twig");
            }
            $enemy->setId($id);
        }
            $troops = $this->troopManager->selectAll();
            $castle = $this->castleManager->selectOneById(1);
            return $this->twig->render("Game/troop.html.twig", ["troops" => $troops, "enemy" => $enemy,
            "castle" => $castle]);
    }
}
