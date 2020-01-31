<?php


namespace Utils;


use Core\Error;
use Core\ViewLoader;

class ParameterValidator
{
    public static function validateParamsOrDie(array $expected, array $parameters): void
    {
        if (!self::validateParams($expected, $parameters)) {
            ViewLoader::getInstance()->render(
                'Error/ErrorPage.html',
                ['error' => new Error(400, "Requete invalide.")]
            );
            die(400);
        }
    }

    public static function validateParams(array $expected, array $parameters): bool
    {
        // Contrôle que tous les parametres attendus sont présent
        foreach ($expected as $param) {
            if (!in_array($param, array_keys($parameters))) {
                return false;
            }
        }

        // Controle qu'aucun parametre n'est vide
        foreach ($parameters as $param) {
            if (empty($param)) {
                return false;
            }
        }
        return true;
    }
}