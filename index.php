<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require __DIR__ . '/vendor/autoload.php';

use app\core\Router;

$controllerRouter = new Router();
require_once "app/config/routes.php";

$controllerRouter->dispatch(strtok($_SERVER['REQUEST_URI'], "?"));
