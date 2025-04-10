<?php

require_once 'controllers/MainController.php';
require_once 'controllers/AdminController.php';
require_once 'database/DBSession.php';
require_once 'routing/Request.php';
require_once 'routing/Router.php';
require_once 'routes.php';

$config = json_decode(file_get_contents("config.json"), true);

$dbConfig = $config["database"];
$session = new DBSession($dbConfig["host"], $dbConfig["port"], $dbConfig["username"], $dbConfig["password"], $dbConfig["dbname"]);
$templateEngine = new TemplateEngine(__DIR__.'/public/views');

$router = new Router($session, $templateEngine);

foreach ($ROUTES as $route) {
    $router->addRoute($route['method'], $route['path'], $route['controllerClass'], $route['controllerMethod']);
}

$router->route(Request::fromCurrentRequest());
