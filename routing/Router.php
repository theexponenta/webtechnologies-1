<?php


require_once __DIR__.'/../database/DBSession.php';
require_once 'Route.php';


class Router {

    private array $routes;
    private DBSession $dbSession;

    public function __construct(DBSession $dbSession) {
        $this->routes = [];
        $this->dbSession = $dbSession;
    }

    public function route($path, $method) {
        $route = $this->getRoute($path, $method);
        $controller = new ($route->getControllerClass())($this->dbSession);
        
        $controllerMethod = $route->getControllerMethod();
        $controller->$controllerMethod();
    }

    public function addRoute(string $method, string $path, $contollerClass, $controllerMethod) {
        array_push($this->routes, new Route($method, $path, $contollerClass, $controllerMethod));
    }

    private function getRoute($path, $method) {
        foreach ($this->routes as $route) {
            if ($route->getPath() == $path && $route->getMethod() == $method)
                return $route;
        }
    }
}
