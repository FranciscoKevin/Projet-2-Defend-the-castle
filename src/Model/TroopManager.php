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
class TroopManager extends AbstractManager
{
    /**
     *
     */
    public const TABLE = 'troop';
    public const ERROR = -1;

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(Troop $troop)
    {
        // prepared request
        $insert = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (name, strength) VALUES (:name, :strength)");
        if (
            false == $insert ||
            false == $insert->bindValue('name', $troop->getName(), PDO::PARAM_STR) ||
            false == $insert->bindValue('strength', $troop->getLevel(), PDO::PARAM_INT)
        ) {
                return self::ERROR;
        } else {
            if ($insert->execute()) {
                return (int)$this->pdo->lastInsertId();
            }
        }
        return "";
    }

    public function deleteAll()
    {
        // prepared request
        $truncate = $this->pdo->prepare("TRUNCATE " . self::TABLE);
        if (false == $truncate) {
            return self::ERROR;
        } else {
            $truncate->execute();
        }
        return "";
    }
}
