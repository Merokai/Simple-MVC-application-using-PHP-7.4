<?php


namespace Controllers;


use Core\Error;
use Core\ViewLoader;
use Exception;
use Repositories\UserRepository;
use Utils\ParameterValidator;
use Utils\RoleValidator;

class UserController
{
    public function getIndex()
    {
        // Accès restreint aux admins
        RoleValidator::validateRoleOrDie(["admin"]);

        $users = UserRepository::getInstance()->findAll();
        ViewLoader::getInstance()->render(
            'Users/Users.html',
            [
                "users" => $users
            ]
        );
    }

    public function getDelete(array $params): void
    {
        // Accès restreint aux admins
        RoleValidator::validateRoleOrDie(["admin"]);

        // Contrôle la présence des paramètres attendus
        ParameterValidator::validateParamsOrDie(['id'], $params);

        $id = intval($params['id']);
        try {
            $user = UserRepository::getInstance()->findByEmail($id);
            if ($_SESSION["user"]["role"] == "admin") {
                UserRepository::getInstance()->deleteByEmail($id);
                header("Location: /user");
            }
        } catch (Exception $ex) {
            ViewLoader::getInstance()->render(
                'Error/ErrorPage.html',
                ['error' => new Error(500, "Impossible de supprimer l'utilisateur.")]
            );
        }
    }

    public function getPromote(array $params): void
    {
        // Accès restreint aux admins
        RoleValidator::validateRoleOrDie(["admin"]);

        // Contrôle la présence des paramètres attendus
        ParameterValidator::validateParamsOrDie(['id'], $params);

        $id = intval($params['id']);
        try {
            $user = UserRepository::getInstance()->findByEmail($id);
            if ($_SESSION["user"]["role"] == "admin") {
                UserRepository::getInstance()->promoteByEmail($id);
                header("Location: /user");
            }
        } catch (Exception $ex) {
            ViewLoader::getInstance()->render(
                'Error/ErrorPage.html',
                ['error' => new Error(500, "Impossible de promouvoir l'utilisateur.")]
            );
        }
    }

    public function getDemote(array $params): void
    {
        // Accès restreint aux admins
        RoleValidator::validateRoleOrDie(["admin"]);

        // Contrôle la présence des paramètres attendus
        ParameterValidator::validateParamsOrDie(['id'], $params);

        $id = intval($params['id']);
        try {
            $user = UserRepository::getInstance()->findByEmail($id);
            if ($_SESSION["user"]["role"] == "admin") {
                UserRepository::getInstance()->demoteByEmail($id);
                header("Location: /user");
            }
        } catch (Exception $ex) {
            ViewLoader::getInstance()->render(
                'Error/ErrorPage.html',
                ['error' => new Error(500, "Impossible de retrograder l'utilisateur.")]
            );
        }
    }

}