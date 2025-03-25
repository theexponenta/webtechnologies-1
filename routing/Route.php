<?php

class Route {

    private string $method;
    private string $path;
    private $controllerClass;
    private $controllerMethod;

    public function __construct(string $method, string $path, $controllerClass, $controllerMethod) {
        $this->method = strtoupper($method);
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
