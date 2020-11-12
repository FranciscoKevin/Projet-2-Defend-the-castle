<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 26/10/20
 * Time: 9:00
 */

namespace App\Model;

use PDO;
use PDOException;

/**
 * This class allows you to insert the properties of the enemy, created with the Troop class, in the database.
 */
class EnemyManager extends AbstractManager
{
    public const TABLE = "enemy";
    public const ERROR = -1;

    /**
     * This method adds the enemy table to the constructor of the class inherited from the parent class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /**
     * This method allows you to insert the properties of the enemy in the database.
     * It returns id number if the enemy properties are correctly inserted,
     * and -1 if there is a problem with the database.
     */
    public function insertEnemy(Troop $enemy): int
    {
        try {
            $insertEnemy = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(name, strength) VALUES (:name, :strength)");
        } catch (PDOException $error) {
            return self::ERROR;
        }
        if (
            false !== $insertEnemy &&
            false !== $insertEnemy->bindValue("name", $enemy->getName(), PDO::PARAM_STR) &&
            false !== $insertEnemy->bindValue("strength", $enemy->getStrength(), PDO::PARAM_INT)
        ) {
            if ($insertEnemy->execute()) {
                return (int)$this->pdo->lastInsertId();
            }
        }
        return self::ERROR;
    }

    /**
     * This method allows you to select the enemy from the database.
     * If the enemy troop does not exist, the method creates it.
     */
    public function selectCurrent(): ?Troop
    {
        $troops = $this->selectAll();
        if (empty($troops)) {
            return null;
        }
        $troop = new Troop();
        $troop->setId($troops[0]["id"]);
        $troop->setName($troops[0]["name"]);
        $troop->setStrength($troops[0]["strength"]);
        return $troop;
    }
}
