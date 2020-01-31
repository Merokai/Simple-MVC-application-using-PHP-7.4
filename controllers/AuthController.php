<?php


namespace Controllers;

use Core\Error;
use Core\ViewLoader;
use Exception;
use Models\User;
use Repositories\UserRepository;
use Utils\ParameterValidator;
use Utils\RoleValidator;

/**
 * Class AuthController
 * @package Controllers
 */
class AuthController
{
    /**
     * GET /auth/login
     * Rendu du formulaire de connexion
     */
    public function getLogin(): void
    {
        ViewLoader::getInstance()->render(
            'Auth/Login.html',
            []
        );
        return;
    }

    /**
     * POST /auth/login
     * @param $params : Array => ['email'], ['password']
     * Soumission du formulaire de connexion
     */
    public function postLogin($params): void
    {
        // Accès interdit aux utilsateurs authentifiés
        RoleValidator::validateRoleOrDie([]);

        // Contrôle la présence des paramètres attendus
        ParameterValidator::validateParamsOrDie(['email', 'password'], $params);

        try {
            // Fetch user from DB
            $user = UserRepository::getInstance()->findByEmail($params['email']);
            // Verify password
            if (password_verify($params['password'], $user->password)) {
                $_SESSION["user"] = [
                    "email" => $user->getEmail(),
                    "displayName" => $user->getDisplayName(),
                    "role" => $user->getRole()
                ];
                header('Location: /auth/login');
            } else {
                ViewLoader::getInstance()->render('Error/ErrorPage.html',
                    ['error' => new Error(404, "Mot de passe invalide")]);
            }
        } catch (Exception $ex) {
            ViewLoader::getInstance()->render('Error/ErrorPage.html',
                ['error' => new Error($ex->getCode(), $ex->getMessage())]);
        }
    }

    /**
     * GET /auth/logout
     * Fin de la session
     */
    public function getLogout(): void
    {
        session_destroy();
        header('Location: /');
    }

    /**
     * POST /auth/register
     * @param $params : Array => ['email'], ['displayName'], ['password']
     * Soumission du formulaire d'inscription
     */
    public function postRegister($params): void
    {
        // Accès interdit aux utilsateurs authentifiés
        RoleValidator::validateRoleOrDie([]);

        // Contrôle la présence des paramètres attendus
        ParameterValidator::validateParamsOrDie(['email', 'displayName', 'password'], $params);

        $user = new User();
        $user->setEmail($params['email']);
        $user->setDisplayName($params['displayName']);
        $user->setPassword(password_hash($params['password'], PASSWORD_BCRYPT));
        $user->setRole("visitor");
        if (UserRepository::getInstance()->createUser($user)) {
            $_SESSION["user"] = [
                "email" => $user->getEmail(),
                "displayName" => $user->getDisplayName(),
                "role" => $user->getRole()
            ];
            header("Location: /auth/login");
        } else {
            ViewLoader::getInstance()->render(
                'Error/ErrorPage.html',
                ['error' => new Error(409, "Impossible de créer l'utilisateur. L'adresse email est peutêtre déja utilisée?")]
            );
        }

    }

    /**
     * GET /auth/register
     * Rendu du formulaire d'inscription
     */
    public function getRegister(): void
    {
        // Accès interdit aux utilsateurs authentifiés
        RoleValidator::validateRoleOrDie([]);

        ViewLoader::getInstance()->render(
            'Auth/Register.html',
            []
        );


    }
}