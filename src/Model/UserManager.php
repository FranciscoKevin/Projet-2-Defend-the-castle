<?php

namespace App\Model;

use PDO;

class UserManager extends AbstractManager
{
    public const TABLE = 'users';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insertUser($name, $mail, $password) //register
    {
        $insertUser = $this->pdo->prepare("INSERT INTO ". self::TABLE . "(username, password, mail) VALUES (:username, :password, :mail)");
        $insertUser->bindValue(":username", $name, PDO::PARAM_STR);
        $insertUser->bindValue(":password", $password, PDO::PARAM_STR);
        $insertUser->bindValue(":mail", $mail, PDO::PARAM_STR);
        $insertUser->execute();
    }

    public function findUser($mail)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE mail=:mail");
        $stmt->bindvalue(":mail", $mail, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
