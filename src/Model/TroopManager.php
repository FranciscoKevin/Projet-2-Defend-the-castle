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
 * This class allows you to insert the properties of the troops, created with the Troop class, in the database.
 */
class TroopManager extends AbstractManager
{
    public const TABLE = "troop";

    /**
     * This method adds the troop table to the constructor of the class inherited from the parent class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /**
     * This method allows you to insert the properties of the troops in the database.
     * It returns true if the troops properties are correctly inserted,
     * and false if there is a problem with the database.
     */
    public function insert(Troop $troop): bool
    {
        try {
            $insert = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(name, strength, tiredness) VALUES (:name, :strength, :tiredness)");
        } catch (PDOException $error) {
            return false;
        }
        if (
            false !== $insert &&
            false !== $insert->bindValue("name", $troop->getName(), PDO::PARAM_STR) &&
            false !== $insert->bindValue("strength", $troop->getStrength(), PDO::PARAM_INT) &&
            false !== $insert->bindValue("tiredness", $troop->getTiredness(), PDO::PARAM_INT)
        ) {
            return $insert->execute();
        }
        return false;
    }

    public function updateTroop(array $defender)
    {
        try {
            $update = $this->pdo->prepare("UPDATE " . self::TABLE . " SET tiredness = :tiredness, 
            strength = :strength WHERE id = :id");
        } catch (PDOException $error) {
            return false;
        }
        if (
            false !== $update &&
            false !== $update->bindValue("id", $defender['id'], PDO::PARAM_STR) &&
            false !== $update->bindValue("tiredness", $defender['tiredness'], PDO::PARAM_INT) &&
            false !== $update->bindValue("strength", $defender['strength'], PDO::PARAM_INT)
        ) {
            return $update->execute();
        }
        return false;
    }
}
