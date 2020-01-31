<?php
session_start();
require_once "vendor/autoload.php";

use Core\Router;

Router::getInstance()->route($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"], $_POST);
?>