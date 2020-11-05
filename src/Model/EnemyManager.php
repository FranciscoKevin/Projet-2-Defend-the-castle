<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 26/10/20
 * Time: 9:00
 */

namespace App\Model;

use PDO;

/**
 * This class Manager is for manage the attackers
 */
class EnemyManager extends AbstractManager
{
    /**
     * constant table in database and ERROR
     */
    const TABLE = 'enemy';
    const ERROR = -1;

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }
   
    public function insertEnemy(Troop $enemy): int
    {
        // prepared request
        $insertEnemy = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            " (name, strength) VALUES (:name, :strength)");
        if ((false == $insertEnemy)
            || (false == $insertEnemy->bindValue('name', $enemy->getName(), PDO::PARAM_STR))
            || (false == $insertEnemy->bindValue('strength', $enemy->getLevel(), PDO::PARAM_INT))) {
                return self::ERROR;
        } else {
            if ($insertEnemy->execute()) {
                return (int)$this->pdo->lastInsertId();
            }
        }
        return self::ERROR;
    }
    //Delete attacker from database when we call it
    public function deleteAttacker()
    {
        $truncate = $this->pdo->prepare("TRUNCATE " . self::TABLE);
        if (false == $truncate) {
            return self::ERROR;
        } else {
            return $truncate->execute();
        }
    }
    //select the attacker from database
    public function selectCurrent() : ?Troop
    {
        $troops = $this->selectAll();
        if (empty($troops)) {
            return null;
        }
        $troop = new Troop();
        $troop->setId($troops[0]["id"]);
        $troop->setName($troops[0]['name']);
        $troop->setLevel($troops[0]['strength']);
        return $troop;
    }
}
