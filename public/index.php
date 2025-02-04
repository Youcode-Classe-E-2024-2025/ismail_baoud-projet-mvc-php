<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../vendor/autoload.php';
require_once '../app/config/config.php';

// Initialize the router
$router = new app\core\Router();
require_once '../app/config/routes.php';
$router->dispatch($_SERVER['REQUEST_URI']);
?>
