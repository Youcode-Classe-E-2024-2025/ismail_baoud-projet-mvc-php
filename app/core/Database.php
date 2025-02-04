<?php

namespace app\core;

class Database {
    private $host;
    private $dbname;
    private $user;
    private $password;
    private $pdo;

    public function __construct() {
        $config = require '../config/config.php';
        $this->host = $config['host'];
        $this->dbname = $config['dbname'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->connect();
    }

    private function connect() {
        try {
            $this->pdo = new PDO("pgsql:host={$this->host};dbname={$this->dbname}", $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}

?>
