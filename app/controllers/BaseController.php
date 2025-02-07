<?php
namespace app\controllers;

class BaseController {

    public function __construct(){

    }
    protected function logMessage($message) {
        $logFile = __DIR__ . '/logs/app.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
    }

    protected function handleError(\Exception $e) {
        $this->logMessage("Error: " . $e->getMessage());
    }

    protected function redirect($url) {
        header("Location: " . $url);
        exit;
    }
}
