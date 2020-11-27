<?php

/**
 * Created by VisualStudioCode.
 * User: Thomas
 * Date: 30/10/2020
 * Time: 19:00
 */

namespace App\Model;

use PDO;
use PDOException;

/**
 * This class allows you to insert the properties of the castle, created with the Castle class, in the database.
 */
class CastleManager extends SubAbstractManager
{
    public const TABLE = "castle";

    /**
     * This method adds the castle table to the constructor of the class inherited from the parent class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /**
     * This method allows you to insert the properties of the castle in the database.
     * It returns true if the castle properties are correctly inserted,
     * and false if there is a problem with the database.
     */
    public function insert(Castle $castle): bool
    {
        try {
            $insert = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(name, score) VALUES (:name, :score)");
        } catch (PDOException $error) {
            return false;
        }
        if (
            false !== $insert &&
            false !== $insert->bindValue("name", $castle->getName(), PDO::PARAM_STR) &&
            false !== $insert->bindValue("score", $castle->getScore(), PDO::PARAM_INT)
        ) {
            return $insert->execute();
        }
        return false;
    }

    /**
     * This method allows you to modify the score of the castle following a battle.
     * As input you need an array which is retrieved from the database.
     */
    public function updateScore(array $castle): bool
    {
        try {
            $insert = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(name, score) VALUES (:name, :score)");
        } catch (PDOException $error) {
            return false;
        }
        if (
            false !== $insert &&
            false !== $insert->bindValue("name", $castle["name"], PDO::PARAM_STR) &&
            false !== $insert->bindValue("score", $castle["score"], PDO::PARAM_INT)
        ) {
            return $insert->execute();
        }
        return false;
    }

    /**
     * This method allows you to add a custom background to the game.
     * It returns a string which allows you to define the status of the return: no upload file,
     * error upload or error type file.
     */
    public function uploadBackground(): string
    {
        $uploadDir = "upload/";
        $allowed = [
            "jpg" => "image/jpg",
            "jpeg" => "image/jpeg",
            "gif" => "image/gif",
            "png" => "image/png"
        ];
        if (
            $_SERVER["REQUEST_METHOD"] == "POST" &&
            $_FILES["bg-castle-upload"]["name"] != ""
        ) {
            if (
                isset($_FILES["bg-castle-upload"]) &&
                $_FILES["bg-castle-upload"]["error"] === 0
            ) {
                $filename = $_FILES["bg-castle-upload"]["name"];
                $filesize = $_FILES["bg-castle-upload"]["size"];
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                if (!array_key_exists($extension, $allowed)) {
                    return "error type file";
                }
                $uploadFile = $uploadDir . "bg-castle-upload." . $extension;
                if (file_exists($uploadFile)) {
                    unlink($uploadFile);
                }
                $uploadFile = $uploadDir . "bg-castle-upload.png";
                $maxsize = 2000000;
                if ($filesize > $maxsize) {
                    return "error upload";
                }
                move_uploaded_file($_FILES["bg-castle-upload"]["tmp_name"], $uploadFile);
                return "upload ok";
            } else {
                return "error upload";
            }
        } else {
            return "no file upload";
        }
    }
}
