<?php

require_once 'controllers/MainController.php';
require_once 'database/DBSession.php';
require_once 'Router.php';


$config = json_decode(file_get_contents("config.json"), true);

$dbConfig = $config["database"];
$session = new DBSession($dbConfig["host"], $dbConfig["port"], $dbConfig["username"], $dbConfig["password"], $dbConfig["dbname"]);

$router = new Router($session);
$router->addRoute('GET', '/', MainController::class, 'view');

$router->route($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
