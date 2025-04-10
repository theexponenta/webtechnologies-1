<?php

require_once 'controllers/MainController.php';
require_once 'controllers/AdminController.php';
require_once 'database/DBSession.php';
require_once 'routing/Request.php';
require_once 'routing/Router.php';

$config = json_decode(file_get_contents("config.json"), true);

$dbConfig = $config["database"];
$session = new DBSession($dbConfig["host"], $dbConfig["port"], $dbConfig["username"], $dbConfig["password"], $dbConfig["dbname"]);
$templateEngine = new TemplateEngine(__DIR__.'/public/views');

$router = new Router($session, $templateEngine);
$router->addRoute('GET', '/', MainController::class, 'view');
$router->addRoute('GET', '/admin', AdminController::class, 'action');
$router->addRoute('POST', '/admin', AdminController::class, 'action');

$router->route(Request::fromCurrentRequest());
