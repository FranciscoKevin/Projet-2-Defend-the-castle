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
    private $enemyManager;
    private $castleManager;

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
     * This method is used to initialize the castle.
     * The properties of the castle are then recorded in the database.
     * This method returns a string and does a redirect.
     */
    public function initCastle(): string
    {
        if (false === $this->castleManager->deleteAll()) {
            header("HTTP/1.1 503 Service Unavailable");
            return $this->twig->render("Error/503.html.twig");
        }
        $castle = new Castle();
        $castle->resetScore();
        if (filter_has_var(INPUT_POST, "castle")) {
            $castle->setName($_POST["castle"]);
        } elseif (
            filter_has_var(INPUT_POST, "my-castle") &&
            $_POST["my-castle"] != ""
        ) {
            $castle->setName($_POST["my-castle"]);
        } else {
            $castle->setName("Defend the Castle");
        }
        if (false === $this->castleManager->insert($castle)) {
            header("HTTP/1.1 503 Service Unavailable");
            return $this->twig->render("Error/503.html.twig");
        }

        $castleBackground = $this->castleManager->uploadBackground();
        $files = $_FILES;
        if (
            $castleBackground === "error upload" ||
            $castleBackground === "error type file"
        ) {
            return $this->twig->render(
                "Game/troop.html.twig",
                ["castleBackground" => $castleBackground,
                "files" => $files]
            );
        }
        header('Location: /game/initTroop');
        return "";
    }

    /**
     * This method is used to initialize the troops.
     * The properties of the troop are then recorded in the database.
     * This method returns a string and does a redirect.
     */
    public function initTroop(): string
    {
        if (
            false === $this->troopManager->deleteAll() ||
            false === $this->enemyManager->deleteAll()
        ) {
            header("HTTP/1.1 503 Service Unavailable");
            return $this->twig->render("Error/503.html.twig");
        }

        $troops[0] = new Troop();
        $troops[0]->setName("Archer");
        $troops[0]->setRandomStrength();
        $troops[0]->resetTiredness();
        $troops[1] = new Troop();
        $troops[1]->setName("Horseman");
        $troops[1]->setRandomStrength();
        $troops[1]->resetTiredness();
        $troops[2] = new Troop();
        $troops[2]->setName("Knight");
        $troops[2]->setRandomStrength();
        $troops[2]->resetTiredness();
        shuffle($troops);

        foreach ($troops as $troop) {
            if (false === $this->troopManager->insert($troop)) {
                header("HTTP/1.1 503 Service Unavailable");
                return $this->twig->render("Error/503.html.twig");
            }
        }
        header('Location: /game/play');
        return "";
    }

    /**
     * This method allows you to select the defender for the battle.
     * and to record in the session the various data of the database necessary for the battle
     * and for the other functions thereafter
     */
    public function selectDefender(int $id)
    {
        /*** Entrances ***/
        $castle = $this->castleManager->selectOneById(1);
        $defenderAll = $this->troopManager->selectAll();
        $defender = $this->troopManager->selectOneById($id);
        $attacker = $this->enemyManager->selectOneById(1);
        /*** Recoreding in session ***/
        $_SESSION["data"]["castle"] = $castle;
        $_SESSION["data"]["defenderAll"] = $defenderAll;
        $_SESSION["data"]["defender"] = $defender;
        $_SESSION["data"]["attacker"] = $attacker;
        /*** Treatment ***/
        $battleOrNot = true;
        /*** Output ***/
        return $this->twig->render(
            "Game/troop.html.twig",
            ["castle" => $castle,
            "defenderAll" => $defenderAll,
            "defender" => $defender,
            "attacker" => $attacker,
            "battleOrNot" => $battleOrNot]
        );
    }

    /**
     * This method allows to know the winner of the battle
     * and saves the data in the session
     */
    public function battle()
    {
        /*** Entrances ***/
        $castle = $_SESSION["data"]["castle"];
        $defenderAll = $_SESSION["data"]["defenderAll"];
        $defender = $_SESSION["data"]["defender"];
        $attacker = $_SESSION["data"]["attacker"];
        /*** Treatment ***/
        $battle = [];
        $bonus = Troop::bonus($attacker['name'], $defender['name']);
        $tirednessImpact = Troop::tirednessImpact($defender["tiredness"]);
        if ($defender["strength"] + $bonus - $tirednessImpact > $attacker["strength"]) {
            $battle["score"] = ($defender["strength"] + $bonus - $tirednessImpact) - $attacker["strength"];
            $battle["result"] = $defender["name"] . " WIN";
        } elseif ($defender["strength"] + $bonus - $tirednessImpact < $attacker["strength"]) {
            $battle["score"] = ($defender["strength"] + $bonus - $tirednessImpact) - $attacker["strength"];
            $battle["result"] = $defender["name"] . " LOSE";
        } else {
            $battle["score"] = 0;
            $battle["result"] = "DRAW";
        }
        $battleFought = true;
        /*** Recoreding in session ***/
        $_SESSION["data"]["defender"] = $defender;
        $_SESSION["data"]["attacker"] = $attacker;
        $_SESSION["data"]["battle"] = $battle;
        /*** Output ***/
        return $this->twig->render(
            "Game/troop.html.twig",
            ["castle" => $castle,
            "defenderAll" => $defenderAll,
            "defender" => $defender,
            "attacker" => $attacker,
            "battle" => $battle,
            "battleFought" => $battleFought]
        );
    }

    /**
     * This method makes it possible to change the score of the castle
     * and to record the data in the database
     * This method makes it possible to calculate the fatigue of the troops according
     * to the result of the battle and to record the data in the database
     */
    public function updateScore()
    {
        /*** Entrances ***/
        $castle = $_SESSION["data"]["castle"];
        $defenderAll = $_SESSION["data"]["defenderAll"];
        $defender = $_SESSION["data"]["defender"];
        $battle = $_SESSION["data"]["battle"];
        /*** Treatment ***/
        $newCastleScore = $castle["score"] + $battle["score"];
        $castle["score"] = $newCastleScore;
        $this->castleManager->deleteAll();
        $this->castleManager->updateScore($castle);
        /*** Treatment ***/
        if ($battle["score"] > 0) {
            $defender["tiredness"] -= random_int(0, 10);
        } else {
            $defender["tiredness"] -= random_int(15, 25);
        }
        foreach ($defenderAll as $soldier) {
            if ($soldier['id'] !== $defender['id']) {
                $soldier['tiredness'] += 10;
                if ($soldier['tiredness'] > 100) {
                    $soldier['tiredness'] = 100;
                }
                $this->troopManager->updateTroop($soldier);
            }
        }
        if ($defender['tiredness'] < 0) {
            $defender['tiredness'] = 0;
        }
        $this->troopManager->updateTroop($defender);
        $this->enemyManager->deleteAll();
        /*** Output ***/
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
            $enemy->setRandomStrength();
            $id = $this->enemyManager->insertEnemy($enemy);
            if (EnemyManager::ERROR === $id) {
                header("HTTP/1.1 503 Service Unavailable");
                return $this->twig->render("Error/503.html.twig");
            }
            $enemy->setId($id);
        }
        $defenderAll = $this->troopManager->selectAll();
        $castle = $this->castleManager->selectOneById(1);

        return $this->twig->render(
            "Game/troop.html.twig",
            ["defenderAll" => $defenderAll,
            "enemy" => $enemy,
            "castle" => $castle]
        );
    }

    public function rules(): string
    {
        return $this->twig->render("Game/rules.html.twig");
    }

    public function faq()
    {
        return $this->twig->render('Game/faq.html.twig');
    }

    public function legal(): string
    {
        return $this->twig->render("Game/legal.html.twig");
    }
}
