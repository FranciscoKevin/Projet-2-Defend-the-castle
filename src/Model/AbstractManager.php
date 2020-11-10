<?php

/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 20:52
 * PHP version 7
 */

namespace App\Model;

use App\Model\Connection;

/**
 * Abstract class handling default manager.
 */
abstract class AbstractManager
{
    protected \PDO $pdo;
    protected string $table;
    protected string $className;

    /**
     * Initializes Manager Abstract class.
     */
    public function __construct(string $table)
    {
        $this->table = $table;
        $this->className = __NAMESPACE__ . '\\' . ucfirst($table);
        $this->pdo = (new Connection())->getPdoConnection();
    }

    /**
     * Get all row from database.
     */
    public function selectAll(): array
    {
        return $this->pdo->query("SELECT * FROM " . $this->table)->fetchAll();
    }

    /**
     * Get one row from database by ID.
     */
    public function selectOneById(int $id): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM $this->table WHERE id=:id");
        $statement->bindValue("id", $id, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch();
    }

    /**
     * This method allows you to delete all data from a database.
     */
    public function deleteAll(): int
    {
        return $this->pdo->exec("TRUNCATE " . $this->table);
    }
}
