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

    /**
     * @param  $troop
     * @return int
     */
    public function insert(troop $troop): int
    {
        // prepared request
        $insert = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (name, strength) VALUES (:name, :strength)");
        $insert->bindValue('name', $troop->getName(), PDO::PARAM_STR);
        $insert->bindValue('strength', $troop->getLevel(), PDO::PARAM_INT);

        if ($insert->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }


    /**
     * @param
     */
    public function delete(): void
    {
        // prepared request
        $truncate = $this->pdo->prepare("TRUNCATE " . self::TABLE);
        $truncate->execute();
    }


    /**
     * @param array $item
     * @return bool
     */
    public function update(array $item):bool
    {

        // prepared request
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `title` = :title WHERE id=:id");
        $statement->bindValue('id', $item['id'], PDO::PARAM_INT);
        $statement->bindValue('title', $item['title'], PDO::PARAM_STR);

        return $statement->execute();
    }
}
