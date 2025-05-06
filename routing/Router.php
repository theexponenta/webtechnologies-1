<?php

declare(strict_types=1);


require_once __DIR__.'/../database/DBSession.php';
require_once 'Route.php';
require_once 'Request.php';


class Router {

    private array $routes;
    private DBSession $dbSession;
    private TemplateEngine $templateEngine;

    public function __construct(DBSession $dbSession, TemplateEngine $templateEngine) {
        $this->routes = [];
        $this->dbSession = $dbSession;
        $this->templateEngine = $templateEngine;
    }

    public function route(Request $request) {
        $path = $request->getPath();
        $method = $request->getMethod();

        $route = $this->getRoute($path, $method);
        if (!$route) {
            return;
        }

        $controller = new ($route->getControllerClass())($this->dbSession, $this->templateEngine);
        
        $controllerMethod = $route->getControllerMethod();
        echo $controller->$controllerMethod($request);
    }

    public function addRoute(string $method, string $path, $contollerClass, $controllerMethod) {
        array_push($this->routes, new Route($method, $path, $contollerClass, $controllerMethod));
    }

    private function getRoute($path, $method) {
        foreach ($this->routes as $route) {
            if ($route->getPath() === $path && $route->getMethod() === $method) {
                return $route;
            }
        }
    }
}
