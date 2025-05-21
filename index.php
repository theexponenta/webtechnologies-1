<?php

require_once 'database/DBSession.php';
require_once 'routing/Request.php';
require_once 'routing/Router.php';
require_once 'middlewares/UserSessionMiddleware.php';
require_once 'config/Config.php';
require_once 'routes.php';

session_start();

$config = Config::getConfig();

$dbConfig = $config["database"];
$session = new DBSession($dbConfig["host"], $dbConfig["port"], $dbConfig["username"], $dbConfig["password"], $dbConfig["dbname"]);
$templateEngine = new TemplateEngine(__DIR__.'/public/views');

$router = new Router($session, $templateEngine);
$router->addMiddleware(new UserSessionMiddleware($session));

foreach ($ROUTES as $route) {
    $router->addRoute($route['method'], $route['path'], $route['controllerClass'], $route['controllerMethod']);
}

$session->connect();
$router->route(Request::fromCurrentRequest());
$session->close();
