<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 26/10/20
 * Time: 9:00
 */

namespace App\Model;

use PDO;

class TroopManager extends AbstractManager
{
    public const TABLE = "troop";
    public const ERROR = -1;

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(Troop $troop)
    {
        $insert = $this->pdo->prepare("INSERT INTO " . self::TABLE .
        "(name, strength, tiredness) VALUES (:name, :strength, :tiredness)");
        if (
            false == $insert ||
            false === $insert->bindValue("name", $troop->getName(), PDO::PARAM_STR) ||
            false === $insert->bindValue("strength", $troop->getStrength(), PDO::PARAM_INT) ||
            false === $insert->bindValue("tiredness", $troop->getTiredness(), PDO::PARAM_INT)
        ) {
                return self::ERROR;
        } else {
            if ($insert->execute()) {
                return (int)$this->pdo->lastInsertId();
            }
        }
        return "";
    }
}
