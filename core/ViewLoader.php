<?php


namespace Core;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ViewLoader
{
    private static $instance;

    private $twig;

    private function __construct()
    {
        $loader = new FilesystemLoader('views');
        $this->twig = new Environment($loader);
    }

    public static function getInstance(): ViewLoader
    {
        if (!isset(ViewLoader::$instance)) {
            ViewLoader::$instance = new ViewLoader();
        }

        return ViewLoader::$instance;
    }

    public function render(string $template, array $params)
    {
        $params["user"] = $_SESSION["user"];
        echo $this->twig->render($template, $params);
    }
}