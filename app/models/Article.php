<?php
namespace app\models;

use app\core\Database;
use PDO;

class Article {
    // const STATUS_PUBLISHED = 'published';
    // const STATUS_DRAFT = 'draft';
    // const STATUS_ARCHIVED = 'archived';
    // const STATUS_PENDING = 'pending';
    // const STATUS_REJECTED = 'rejected';

    private $db;

    public function __construct() {
        $this->db = new Database();
    }
    public function create($data) {
        $sql = "INSERT INTO articles (title, content, user_id, category_id, status, created_at, updated_at) 
                VALUES (:title, :content, :user_id, :category_id, :status, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':content', $data['content']);
        $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindValue(':status', $data['status']);
        
        return $stmt->execute();
    }
    public function createArticle($data) {
        return $this->create($data);
    }

    public function getLatestArticles($limit = 6) {
        $stmt = $this->db->getConnection()->prepare(
            "SELECT 
                -- a.id, 
                -- a.title, 
                -- a.content, 
                -- a.user_id,
                -- a.category_id,
                -- a.status,
                -- a.created_at, 
                -- a.updated_at,
                -- c.name as category_name,
                -- u.username as author_name
                *
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC 
            LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $a =  $stmt->fetchAll(PDO::FETCH_ASSOC);
        include_once __DIR__. "app/views/back/dashbaord.twig";
    }

    public function updateArticle($data) {
        $stmt = $this->db->getConnection()->prepare(
            "UPDATE articles 
             SET title = :title, content = :content, category_id = :category_id 
             WHERE id = :id"
        );
        return $stmt->execute([
            'id' => $data['id'],
            'title' => $data['title'],
            'content' => $data['content'],
            'category_id' => $data['category_id']
        ]);
    }

     public function deleteArticle($id) {
        $stmt = $this->db->getConnection()->prepare("DELETE FROM articles WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }


    // public function getAllArticles($page = 1, $limit = 10) {
    //     $offset = ($page - 1) * $limit;
    //     $stmt = $this->db->getConnection()->prepare(
    //         "SELECT 
    //             a.id, 
    //             a.title, 
    //             a.content, 
    //             a.user_id,
    //             a.category_id,
    //             a.status,
    //             a.created_at, 
    //             a.updated_at,
    //             c.name as category_name,
    //             u.username as author_name
    //         FROM articles a 
    //         LEFT JOIN categories c ON a.category_id = c.id 
    //         LEFT JOIN users u ON a.user_id = u.id
    //         ORDER BY a.created_at DESC 
    //         LIMIT :limit OFFSET :offset"
    //     );
    //     $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    //     $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

    // public function getArticlesByUser($userId, $page = 1, $limit = 10) {
    //     $offset = ($page - 1) * $limit;
        
    //     $sql = "SELECT 
    //             a.id, 
    //             a.title, 
    //             a.content, 
    //             a.user_id,
    //             a.category_id,
    //             a.status,
    //             a.created_at, 
    //             a.updated_at,
    //             c.name as category_name,
    //             u.username as author_name
    //         FROM articles a 
    //         LEFT JOIN categories c ON a.category_id = c.id 
    //         LEFT JOIN users u ON a.user_id = u.id
    //         WHERE a.user_id = :user_id 
    //         ORDER BY a.created_at DESC 
    //         LIMIT :limit OFFSET :offset";
               
    //     $stmt = $this->db->getConnection()->prepare($sql);
    //     $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    //     $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    //     $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

    

    // public function getArticleById($id) {
    //     $sql = "SELECT 
    //             a.id, 
    //             a.title, 
    //             a.content, 
    //             a.user_id,
    //             a.category_id,
    //             a.status,
    //             a.created_at, 
    //             a.updated_at,
    //             c.name as category_name,
    //             u.username as author_name
    //         FROM articles a 
    //         LEFT JOIN categories c ON a.category_id = c.id 
    //         LEFT JOIN users u ON a.user_id = u.id
    //         WHERE a.id = :id";
        
    //     $stmt = $this->db->getConnection()->prepare($sql);
    //     $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    //     $stmt->execute();
        
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }

    // public function getTotalArticles() {
    //     $stmt = $this->db->getConnection()->query(
    //         "SELECT COUNT(*) as total FROM articles"
    //     );
    //     $result = $stmt->fetch();
    //     return (int)$result['total'];
    // }

    // public function getTotalCategories() {
    //     $stmt = $this->db->getConnection()->query("SELECT COUNT(*) as total FROM categories");
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //     return (int)$result['total'];
    // }




   
    // public function getPublishedArticles()
    // {
    //     $sql = "SELECT * FROM articles";
    //     $stmt = $this->db->getConnection()->prepare($sql);
    //     $stmt->execute();
    //     return $stmt->fetchAll();
    // }

    // public function getRecentArticles($limit = 5) {
    //     $sql = "SELECT a.*, c.name as category_name, u.username as author 
    //            FROM articles a 
    //            LEFT JOIN categories c ON a.category_id = c.id 
    //            LEFT JOIN users u ON a.user_id = u.id 
    //            ORDER BY a.created_at DESC 
    //            LIMIT :limit";
    //     $stmt = $this->db->getConnection()->prepare($sql);
    //     $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }



    // public function getTotalArticlesByUser($userId) {
    //     // Temporarily return total count of all articles
    //     $stmt = $this->db->getConnection()->prepare("SELECT COUNT(*) as total FROM articles");
    //     $stmt->execute();
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //     return (int)$result['total'];
    // }
}