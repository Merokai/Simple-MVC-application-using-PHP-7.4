<?php


namespace Utils;


use Core\Error;
use Core\ViewLoader;

class RoleValidator
{
    public static function validateRoleOrDie(array $expected): void
    {
        if (!self::validateRole($expected)) {
            ViewLoader::getInstance()->render(
                'Error/ErrorPage.html',
                ['error' => new Error(403, "Non autorisé.")]
            );
            die(400);
        }
    }

    public static function validateRole(array $expected): bool
    {
        // Utilisateur non authentifié: OK si on n'attend aucun role
        if (!isset($_SESSION["user"])) {
            return count($expected) == 0;
        }

        // Utilisateur authentifié: il faut que son rôle soit attendu.
        return in_array($_SESSION["user"]["role"], $expected);
    }
}