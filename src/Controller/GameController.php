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

        if (false === $this->enemyManager->deleteAll()) {
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
        $troops[2]->setName("Lancer");
        $troops[2]->setRandomStrength();
        $troops[2]->resetTiredness();
        shuffle($troops);

        foreach ($troops as $troop) {
            if (false === $this->troopManager->insert($troop)) {
                header("HTTP/1.1 503 Service Unavailable");
                return $this->twig->render("Error/503.html.twig");
            }
        }

        $this->castleManager->deleteAll();
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
     * This method makes it possible to manage the combats between defense and attack.
     * As input, the method takes as argument the id of the selected defense.
     * This makes it possible to recover the defense in the database.
     * Depending on the result of the fight, the castle score is recalculated and sent to the database.
     * We use the same render as the "play" function, conditional structures allow the display
     * of new information passed to the view.
     */

    public function battle(int $id)
    {
        $defender = $this->troopManager->selectOneById($id);
        $attacker = $this->enemyManager->selectOneById(1);
        $castle = $this->castleManager->selectOneById(1);
        $defenderAll = $this->troopManager->selectAll();
        $bonus = Troop::bonus($attacker['name'], $defender['name']);

        if ($defender["strength"] + $bonus > $attacker["strength"]) {
            $defender["tiredness"] -= random_int(10, 25);
            $battleResult = $defender["name"] . " WIN";
        } elseif ($defender["strength"] + $bonus < $attacker["strength"]) {
                $defender["tiredness"] -= ($attacker["strength"] / 2);
                $battleResult = $defender["name"] . " LOOSE";
        } else {
            $battleResult = "DRAW";
        }
        $scoreBattle = (($defender["strength"] + $bonus) - $attacker["strength"]) * 2;
        $newCastleScore = $castle["score"] + $scoreBattle;
        foreach ($defenderAll as $soldier) {
            if ($soldier['id'] !== $defender['id']) {
                $soldier['tiredness'] += 10;
                $soldier['strength'] += 1;
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
        $this->castleManager->deleteAll();
        $castle["score"] = $newCastleScore;
        $this->castleManager->updateScore($castle);
        $this->enemyManager->deleteAll();
        return $this->twig->render("Game/troop.html.twig", [
        "castle" => $castle, "battleResult" => $battleResult, "scoreBattle" => $scoreBattle]);
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
        $troops = $this->troopManager->selectAll();
        $castle = $this->castleManager->selectOneById(1);
        return $this->twig->render("Game/troop.html.twig", ["troops" => $troops, "enemy" => $enemy,
        "castle" => $castle]);
    }

    public function rules(): string
    {
        return $this->twig->render("Game/rules.html.twig");
    }
}
