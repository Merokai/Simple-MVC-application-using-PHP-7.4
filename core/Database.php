<?php

namespace Core;

const DSN = "mysql:host=db;port=3306;dbname=coursphp";
const DB_USER = "coursphp";
const DB_PASSWORD = "coursphp";

use Exception;
use \PDO;

class Database
{
    private static $instance;

    public static function getInstance(): PDO
    {
        if (!isset(Database::$instance)) {
            Database::initDb();
        }
        return Database::$instance;
    }

    private static function initDb(): void
    {
        try {
            Database::$instance = new PDO(
                DSN,
                DB_USER,
                DB_PASSWORD
            );
        } catch (Exception $ex) {
            error_log("Unable to set up db connection: " . DSN);
            die(500);
        }
    }
}