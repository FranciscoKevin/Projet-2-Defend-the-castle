<?php
/**
 * Created by VisualStudioCode.
 * User: Thomas
 * Date: 30/10/2020
 * Time: 19:00
 */

namespace App\Model;

use PDO;

class CastleManager extends AbstractManager
{
    /**
     * Class constants
     */
    const TABLE = 'castle';
    const ERROR = -1;

    /**
     * Class initialization
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

   
    public function insert(Castle $castle)
    {
        // Preparation of the request for the insertion of castle
        $insert = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (name, score) VALUES (:name, :score)");
        // Check if access to the database
        if ((false == $insert)
            || (false === $insert->bindValue('name', $castle->getName(), PDO::PARAM_STR))
            || (false === $insert->bindValue('score', $castle->getScore(), PDO::PARAM_INT))) {
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
        // Preparation of the data deletion request
        $truncate = $this->pdo->prepare("TRUNCATE " . self::TABLE);
        // Check if access to the database
        if (false == $truncate) {
            return self::ERROR;
        } else {
            $truncate->execute();
        }
        return "";
    }
}
