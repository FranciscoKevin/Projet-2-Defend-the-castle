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
use App\Model\Castle;
use App\Model\CastleManager;

class GameController extends AbstractController
{
    private $troopManager;
    private $castleManager;

    public function __construct()
    {
        parent::__construct();
        $this->troopManager = new TroopManager();
        $this->castleManager = new CastleManager();
    }

    public function init(): string
    {
        // Check if access to the database and data deletion
        if (false === $this->troopManager->deleteAll()) {
            header("HTTP/1.1 503 Service Unavailable");
            echo '503 - Service Unavailable';
        }
        // Creation of troops with their random level
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
            }
        }

        // Check if access to the database and data deletion
        if (false === $this->castleManager->deleteAll()) {
            header("HTTP/1.1 503 Service Unavailable");
            echo '503 - Service Unavailable';
        }
        // Creation of castle
        $castle = new Castle();
        $castle->setName("|||_|||_|||_DEFEND THE CASTLE_|||_|||_|||");
        $castle->setScore();

        // Insertion of castle in the database
        if (false === $this->castleManager->insert($castle)) {
            header("HTTP/1.1 503 Service Unavailable");
            echo '503 - Service Unavailable';
        }

        // Redirection after initialization
        header('Location: /game/play');
        return "";
    }

    public function play():string
    {
        $castle = $this->castleManager->selectOneById(1);
        $troops = $this->troopManager->selectAll();
        return $this->twig->render("Game/troop.html.twig", ["castle" => $castle, "troops" => $troops]);
    }
}
