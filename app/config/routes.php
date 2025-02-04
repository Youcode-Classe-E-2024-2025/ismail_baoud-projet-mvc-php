<?php
use app\core\Router;

$router = new Router();
$router->add('GET', '/', ['controllers\front\HomeController', 'index']);
?>
