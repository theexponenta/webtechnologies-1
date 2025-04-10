<?php

$ROUTES = [
    ["method" => "GET", "path" => "/", "controllerClass" => MainController::class, "controllerMethod" => "view"],
    ["method" => "GET", "path" => "/admin", "controllerClass" => AdminController::class, "controllerMethod" => "action"],
    ["method" => "POST", "path" => "/admin", "controllerClass" => AdminController::class, "controllerMethod" => "action"],
];
