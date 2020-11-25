<?php

/**
 * Created by VisualStudioCode.
 * User: Thomas
 * Date: 18/11/2020
 * Time: 10:00
 */

namespace App\Model;

/**
 * This class extends the AbstractManager class to be able to add methods without breaking the framework.
 */
abstract class SubAbstractManager extends AbstractManager
{
    /**
     * This method allows you to delete all data from a database.
     */
    public function deleteAll(): int
    {
        return $this->pdo->exec("TRUNCATE " . $this->table);
    }
}
