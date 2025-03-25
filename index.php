<?php

require_once 'controllers/MainController.php';
require_once 'database/DBSession.php';
require_once 'Router.php';


$config = json_decode(file_get_contents("config.json"), true);

$db_config = $config["database"];
$session = new DBSession($db_config["host"], $db_config["port"], $db_config["username"], $db_config["password"], $db_config["dbname"]);

$router = new Router($session);
$router->addRoute('GET', '/', MainController::class, 'view');

$router->route($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
