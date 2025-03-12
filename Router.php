<?php


enum Method {
    case GET;
    case POST;
}


function strToMethod(string $method) {
    $method = strtolower($method);
    if ($method == "get")
        return Method::GET;

    if ($method == "post")
        return Method::POST;
}


class Route {

    private Method $method;
    private string $path;
    private $controllerClass;
    private $controllerMethod;

    public function __construct(Method $method, string $path, $controllerClass, $controllerMethod) {
        $this->method = $method;
        $this->path = $path;
        $this->controllerClass = $controllerClass;
        $this->controllerMethod = $controllerMethod;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getPath() {
        return $this->path;
    }

    public function getControllerClass() {
        return $this->controllerClass;
    }

    public function getControllerMethod() {
        return $this->controllerMethod;
    }
}


class Router {

    private array $routes;
    private DBSession $db_session;

    public function __construct($db_session) {
        $this->routes = [];
        $this->db_session = $db_session;
    }


    public function route($path, $method) {
        $route = $this->getRoute($path, $method);
        $controller = new ($route->getControllerClass())($this->db_session);
        
        $controllerMethod = $route->getControllerMethod();
        $controller->$controllerMethod();
    }

    public function addRoute(Method $method, string $path, $contollerClass, $controllerMethod) {
        array_push($this->routes, new Route($method, $path, $contollerClass, $controllerMethod));
    }

    private function getRoute($path, $method) {
        $enumMethod = strToMethod($method);
        foreach ($this->routes as $route) {
            if ($route->getPath() == $path && $route->getMethod() == $enumMethod)
                return $route;
        }
    }
}
