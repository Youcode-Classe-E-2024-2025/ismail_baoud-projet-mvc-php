<?php

namespace app\models;

use app\core\Database;
use PDO;

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllCategories() {
        $stmt = $this->db->getConnection()->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
