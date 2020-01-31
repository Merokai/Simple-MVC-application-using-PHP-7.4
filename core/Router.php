<?php

namespace Core;

class Router
{
    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance(): Router
    {
        if (!isset($instance)) {
            Router::$instance = new Router();
        }
        return Router::$instance;
    }

    public function route(string $method, string $uri, array $postParams): void
    {
        // Extract route elements
        $route = $this->buildRoute($method, $uri, $postParams);

        if (class_exists($route->getController(), true)) {

            if (in_array($route->getAction(), get_class_methods($route->getController()))) {
                if (is_null($route->getParams())) {
                    call_user_func([$route->getController(), $route->getAction()]);
                } else {
                    call_user_func_array([$route->getController(), $route->getAction()], [$route->getParams()]);
                }
            } else {
                ViewLoader::getInstance()->render('Error/ErrorPage.html',
                    ['error' => new Error(404, "Page introuvable")]);
                return;
            }
        } else {
            ViewLoader::getInstance()->render('Error/ErrorPage.html',
                ['error' => new Error(404, "Page introuvable")]);
            return;
        }
    }

    public function buildRoute(string $method, string $request_uri, array $postParams): Route
    {
        // Transforme l'uri en tableau et retire les élements vides
        $request_uri = explode("/", $request_uri);
        $request = [];
        foreach ($request_uri as $s) {
            if (!empty($s)) {
                array_push($request, $s);
            }
        }
        // Le premier élément indique le controller
        $controller = "Controllers\\" . (sizeof($request) > 0 ? ucfirst(array_shift($request)) . 'Controller' : 'IndexController');
        // Le second (optionel) indique l'action
        $action = strtolower($method) . ucfirst(sizeof($request) > 0 ? array_shift($request) : 'index');

        $params = [];
        foreach ($postParams as $k => $v) {
            $params[$k] = $v;
        }
        // Le troisième (optionel) indique l'id d'un élément
        if (sizeof($request) > 0) {
            $params["id"] = array_shift($request);
        }
        return new Route($controller, $action, $params);
    }
}
