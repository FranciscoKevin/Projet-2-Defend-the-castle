<?php

/**
 * Created by VisualStudioCode.
 * User: Thomas
 * Date: 30/10/2020
 * Time: 19:00
 */

namespace App\Model;

use PDO;
use PDOException;

/**
 * This class allows you to insert the properties of the castle, created with the Castle class, in the database.
 */
class CastleManager extends AbstractManager
{
    public const TABLE = "castle";

    /**
     * This method adds the castle table to the constructor of the class inherited from the parent class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /**
     * This method allows you to insert the properties of the castle in the database.
     * It returns true if the castle properties are correctly inserted,
     * and false if there is a problem with the database.
     */
    public function insert(Castle $castle): bool
    {
        try {
            $insert = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(name, score) VALUES (:name, :score)");
        } catch (PDOException $error) {
            return false;
        }
        if (
            false !== $insert &&
            false !== $insert->bindValue("name", $castle->getName(), PDO::PARAM_STR) &&
            false !== $insert->bindValue("score", $castle->getScore(), PDO::PARAM_INT)
        ) {
            return $insert->execute();
        }
        return false;
    }

    /**
     * This method allows you to modify the score of the castle following a battle.
     * As input you need an array which is retrieved from the database.
     */
    public function updateScore(array $castle): bool
    {
        try {
            $insert = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(name, score) VALUES (:name, :score)");
        } catch (PDOException $error) {
            return false;
        }
        if (
            false !== $insert &&
            false !== $insert->bindValue("name", $castle["name"], PDO::PARAM_STR) &&
            false !== $insert->bindValue("score", $castle["score"], PDO::PARAM_INT)
        ) {
            return $insert->execute();
        }
        return false;
    }
}
