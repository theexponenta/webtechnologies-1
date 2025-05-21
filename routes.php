<?php

declare(strict_types=1);

require_once 'controllers/MainController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/LoginController.php';
require_once 'controllers/RegisterController.php';
require_once 'controllers/SessionController.php';


$ROUTES = [
    ["method" => "GET", "path" => "/", "controllerClass" => MainController::class, "controllerMethod" => "view"],
    ["method" => "GET", "path" => "/admin", "controllerClass" => AdminController::class, "controllerMethod" => "action"],
    ["method" => "POST", "path" => "/admin", "controllerClass" => AdminController::class, "controllerMethod" => "action"],
    ["method" => "GET", "path" => "/login", "controllerClass" => LoginController::class, "controllerMethod" => "view"],
    ["method" => "POST", "path" => "/login", "controllerClass" => SessionController::class, "controllerMethod" => "login"],
    ["method" => "GET", "path" => "/register", "controllerClass" => RegisterController::class, "controllerMethod" => "view"],
    ["method" => "POST", "path" => "/register", "controllerClass" => SessionController::class, "controllerMethod" => "register"],
    ["method" => "POST", "path" => "/logout", "controllerClass" => SessionController::class, "controllerMethod" => "logout"]
];
