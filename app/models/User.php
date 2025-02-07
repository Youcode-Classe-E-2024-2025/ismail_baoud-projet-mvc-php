<?php

namespace app\models;

use app\core\Database;
use PDO;

class User {
    private $db;
    const ROLE_USER = 'user';

    public function __construct() {
        $this->db = new Database();
    }

    public function register($username, $email, $password) {
        try {
            // Check if email already exists
            $checkEmail = $this->db->getConnection()->prepare("SELECT id FROM users WHERE email = :email");
            $checkEmail->execute(['email' => $email]);
            if ($checkEmail->fetch()) {
                throw new \Exception('Email already registered');
            }

            // Check if username already exists
            $checkUsername = $this->db->getConnection()->prepare("SELECT id FROM users WHERE username = :username");
            $checkUsername->execute(['username' => $username]);
            if ($checkUsername->fetch()) {
                throw new \Exception('Username already taken');
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
            $stmt = $this->db->getConnection()->prepare($sql);
            $result = $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => self::ROLE_USER
            ]);

            if (!$result) {
                throw new \Exception('Failed to create user');
            }
            return true;
        } catch (\PDOException $e) {
            throw new \Exception('Database error: ' . $e->getMessage());
        }
    }

    public function login($email, $password) {
        try {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new \Exception('Invalid email or password');
            }

            if (isset($user['active']) && !$user['activate']) {
                throw new \Exception('Account is deactivated');
            }

            if (!password_verify($password, $user['password'])) {
                throw new \Exception('Invalid email or password');
            }
            var_dump($user);
            return $user;
        } catch (\PDOException $e) {
            throw new \Exception('Database error: ' . $e->getMessage());
        }
    }

    public function getTotalUsers() {
        $stmt = $this->db->getConnection()->query("SELECT COUNT(*) as total FROM users");
        $result = $stmt->fetch();
        return (int)$result['total'];
    }

    public function getRecentUsers($limit = 5) {
        $stmt = $this->db->getConnection()->prepare(
            "SELECT id, username, email, role, created_at 
            FROM users 
            ORDER BY created_at DESC 
            LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNewUsersCount($days) {
        $stmt = $this->db->getConnection()->prepare(
            "SELECT COUNT(*) as total FROM users WHERE created_at >= (NOW() - :days * INTERVAL '1 day')"
        );
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    public function getAllUsers($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM users ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleStatus($userId) {
        try {
            $sql = "UPDATE users
SET status = CASE
    WHEN status = 'active' THEN 'not'
    WHEN status = 'not' THEN 'active'
    ELSE status 
END
where id = :id";
            $stmt = $this->db->getConnection()->prepare($sql);
            return $stmt->execute(['id' => $userId]);
        } catch (\PDOException $e) {
            throw new \Exception('Failed to toggle user status: ' . $e->getMessage());
        }
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateUser($id, $data) {
        $allowedFields = ['username', 'email', 'role', 'active'];
        $updates = [];
        $values = [];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updates[] = "`$field` = ?";
                $values[] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $values[] = $id;

        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute($values);
    }

    public function deleteUser($id) {
        $stmt = $this->db->getConnection()->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateProfile($userId, $data) {
        $allowedFields = ['username', 'email', 'bio'];
        $updates = [];
        $values = [];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updates[] = "`$field` = ?";
                $values[] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $values[] = $userId;
        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute($values);
    }

    public function updatePassword($userId, $currentPassword, $newPassword) {
        $user = $this->getUserById($userId);
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return false;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$hashedPassword, $userId]);
    }

    public function updateUserRole($userId, $newRole) {
        if (!in_array($newRole, [self::ROLE_USER, self::ROLE_ADMIN])) {
            return false;
        }

        $sql = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$newRole, $userId]);
    }

    public function deactivateUser($userId) {
        $sql = "UPDATE users SET active = 0 WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$userId]);
    }

    public function activateUser($userId) {
        $sql = "UPDATE users SET active = 1 WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$userId]);
    }
}