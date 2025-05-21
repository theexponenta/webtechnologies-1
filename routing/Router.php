<?php

declare(strict_types=1);


require_once __DIR__.'/../database/DBSession.php';
require_once 'Route.php';
require_once 'Request.php';


class Router {

    private array $routes;
    private DBSession $dbSession;
    private TemplateEngine $templateEngine;
    private array $middlewareStack;

    public function __construct(DBSession $dbSession, TemplateEngine $templateEngine) {
        $this->routes = [];
        $this->dbSession = $dbSession;
        $this->templateEngine = $templateEngine;
        $this->middlewareStack = [];
    }

    private function handle(Request $request, Route $route) {
        $controller = new ($route->getControllerClass())($this->dbSession, $this->templateEngine);
        $controllerMethod = $route->getControllerMethod();
        echo $controller->$controllerMethod($request);
    }

    public function route(Request $request) {
        $path = $request->getPath();
        $method = $request->getMethod();

        $route = $this->getRoute($path, $method);
        if (!$route) {
            return;
        }

        $middlewareChain = array_reduce(
            array_reverse($this->middlewareStack),
            fn($next, $middleware) => fn(Request $req) => $middleware($req, $next),
            fn(Request $req) => $this->handle($req, $route)
        );

        $middlewareChain($request);
    }

    public function addRoute(string $method, string $path, $contollerClass, $controllerMethod) {
        array_push($this->routes, new Route($method, $path, $contollerClass, $controllerMethod));
    }

        public function addMiddleware($middleware) {
        array_push($this->middlewareStack, $middleware);
    }

    private function getRoute($path, $method) {
        foreach ($this->routes as $route) {
            if ($route->getPath() === $path && $route->getMethod() === $method) {
                return $route;
            }
        }
    }
}
