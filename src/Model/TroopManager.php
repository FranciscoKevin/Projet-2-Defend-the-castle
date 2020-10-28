<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
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
    const TABLE = 'troop';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(Troop $troop): int
    {
        // prepared request
        $insert = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (name, strength) VALUES (:name, :strength)");
        $insert->bindValue('name', $troop->getName(), PDO::PARAM_STR);
        $insert->bindValue('strength', $troop->getLevel(), PDO::PARAM_INT);

        if ($insert->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    public function delete(): void
    {
        // prepared request
        $truncate = $this->pdo->prepare("TRUNCATE " . self::TABLE);
        $truncate->execute();
    }
}
