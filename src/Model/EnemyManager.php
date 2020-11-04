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
 *
 */
class EnemyManager extends AbstractManager
{
    /**
     *
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
        $insertEnemy = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (name, strength) VALUES (:name, :strength)");
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
    
    public function deleteAttacker()
    {
        $truncate = $this->pdo->prepare("TRUNCATE " . self::TABLE);
        if (false == $truncate) {
            return self::ERROR;
        } else {
            $truncate->execute();
        }
        return "";
    }

    public function selectCurrent() : ?Troop
    {
        $troops = $this->selectAll();
        if(empty($troops)) {
            return null;
        }
        $troop = new Troop();
        $troop->setId($troops[0]["id"]);
        $troop->setName($troops[0]['name']);
        $troop->setLevel($troops[0]['strength']);
        return $troop;

    }
}

