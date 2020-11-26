<?php

namespace App\Controller;

use App\Model\UserManager;

class UserController extends AbstractController
{
    public function register()
    {
        session_start();
        // Si la variable "$_Post" contient des informations alors on les traitres
        if (!empty($_POST['submit'])) {
            $valid = true;
            $name = htmlentities(trim($_POST['username']));
            $mail = htmlentities(trim($_POST['mail']));
            $password = trim($_POST['password']);
            $confPass = trim($_POST['verifyPassword']);
            // Vérification du nom
            if (empty($name)) {
                $valid = false;
                $nameError = "Enter your blaze";
                echo $nameError;
            }
            // Vérification du mail
            if (empty($mail)) {
                $valid = false;
                $mailError = "Le mail ne peut pas être vide";
                echo $mailError;
            } elseif (!preg_match("/^[a-z0-9\-_.]+@[a-z]+\.[a-z]{2,3}$/i", $mail)) {
                $valid = false;
                $mailError = "Le mail n'est pas valide";
                echo $mailError;
            }

            // Vérification du mdp
            if (empty($password)) {
                $valid = false;
                $passError = "Le mot de passe ne peut pas être vide";
                echo $passError;
            } elseif ($password != $confPass) {
                $valid = false;
                $passError = "La confirmation du mot de passe ne correspond pas";
                echo $passError;
            }
            // Si toutes les conditions sont remplies alors on fait le traitement
            if ($valid) {
                $password = password_hash($password, PASSWORD_DEFAULT);
                $userRegister = new UserManager();
                try {
                    $userRegister->insertUser($name, $mail, $password);
                    header('Location: /user/login');
                } catch (\PDOException $e) {
                    return $this->twig->render("Error/503.html.twig", [
                        'errormsg' => "Cette adresse mail est déja utilisée."
                        ]);
                }
            }
        }
        return $this->twig->render("Game/register.html.twig");
    }

    public function login()
    {
        session_start();
        if (!empty($_POST['submit'])) {
            $valid = true;
            $mail = htmlentities(trim($_POST['mail']));
            $password = trim($_POST['password']);
            if (empty($mail)) {
                $valid = false;
                $mailError = "Le mail ne peut pas être vide";
                echo $mailError;
            } elseif (!preg_match("/^[a-z0-9\-_.]+@[a-z]+\.[a-z]{2,3}$/i", $mail)) {
                $valid = false;
                $mailError = "Le mail n'est pas valide";
                echo $mailError;
            }
            if (empty($password)) {
                $valid = false;
                $passError = "Le mot de passe ne peut pas être vide";
                echo $passError;
            }
            if ($valid) {
                $userLog = new UserManager();
                $results = $userLog->findUser($mail);
                if (empty($results)) {
                    return $this->twig->render("Error/503.html.twig", ['errormsg' => "Ce compte n'existe pas."]);
                } else {
                    if (!password_verify($password, $results[0]['password'])) {
                        return $this->twig->render("Error/503.html.twig", ['errormsg' => "Mauvais mot de passe pélo"]);
                    } else {
                        header('Location: /game/play');
                    }
                }
            }
        }
        return $this->twig->render("Game/login.html.twig");
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header("Location: /user/login");
    }
}
