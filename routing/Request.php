<?php

declare(strict_types=1);


class Request {
    private string $method;
    private string $path;
    private array $params;
    private string $body;
    private array | null $formParams;

    function __construct(string $method, string $path, array $params, string $body, array | null $formParams) {
        $this->method = $method;
        $this->path = $path;
        $this->params = $params;
        $this->body = $body;
        $this->formParams = $formParams;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getPath() {
        return $this->path;
    }

    public function getParams() {
        return $this->params;
    }

    public function getBody() {
        return $this->body;
    }

    public function getFormParams() {
        return $this->formParams;
    }

    public function json() {
        return json_decode($this->getBody(), true);
    }

    public static function fromCurrentRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = strtok($_SERVER["REQUEST_URI"], '?');;
        $params = $_GET;
        $body = file_get_contents('php://input');
        $formParams = null;
        if ($method == "POST" && $_SERVER['CONTENT_TYPE'] == 'application/x-www-formurlencoded')
            $formParams = $_POST;

        return new Request($method, $path, $params, $body, $formParams);
    }
}
