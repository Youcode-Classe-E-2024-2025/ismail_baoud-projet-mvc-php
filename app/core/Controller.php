<?php
namespace app\core;

class Controller {
    protected $view;

    public function __construct() {
        $this->view = new View();
    }

    protected function redirect($url) {
        header('Location: ' . $url);
        exit();
    }
}