<?php

namespace app\controllers\back;

use app\core\Controller;
use app\models\User;
use app\models\Article;
class AdminDashboardController extends Controller {
    private $userModel;
    private $articleModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->articleModel = new Article();
    }

    public function index() {
        // Check if user is admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('/');
        }

        // Get statistics for dashboard
        $stats = [
            'total_users' => $this->userModel->getTotalUsers(),
            'total_articles' => $this->articleModel->getTotalArticles(),
            'new_articles' => $this->articleModel->getNewArticles(),
            'recent_articles' => $this->articleModel->getRecentArticles(5),
            'new_users' => $this->userModel->getNewUsersCount(7), // last 7 days
            'resent_activity' => $this->getRecentActivity()

        ];

        $this->view->render('admin/dashboard.twig', [
            'stats' => $stats
        ]);
    }

    public function users() {
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        
        $users = $this->userModel->getAllUsers($page, $limit);
        $totalUsers = $this->userModel->getTotalUsers();
        
        $this->view->render('admin/users.twig', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'currentPage' => $page,
            'totalPages' => ceil($totalUsers / $limit)
        ]);
    }

    public function viewUser($id) {
        $user = $this->userModel->getUserById($id);
        $userArticles = $this->articleModel->getArticlesByUser($id);
        
        if (!$user) {
            $this->redirect('/admin/users');
        }
        
        $this->view->render('admin/user-view.twig', [
            'user' => $user,
            'articles' => $userArticles
        ]);
    }

    public function updateUser($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users/' . $id);
        }

        $data = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'role' => $_POST['role'] ?? 'user',
            'active' => isset($_POST['active']) ? 1 : 0
        ];

        if ($this->userModel->updateUser($id, $data)) {
            $_SESSION['success'] = 'User updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update user';
        }

        $this->redirect('/admin/users/' . $id);
    }

    public function articles() {
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        
        $articles = $this->articleModel->getAllArticles($page, $limit);
        $totalArticles = $this->articleModel->getTotalArticles();
        
        $this->view->render('admin/articles.twig', [
            'articles' => $articles,
            'totalArticles' => $totalArticles,
            'currentPage' => $page,
            'totalPages' => ceil($totalArticles / $limit)
        ]);
    }

    public function deleteUser($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
        }

        // Prevent deleting self
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'You cannot delete your own account';
            $this->redirect('/admin/users');
        }

        if ($this->userModel->deleteUser($id)) {
            $_SESSION['success'] = 'User deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete user';
        }

        $this->redirect('/admin/users');
    }

    public function toggleUserStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
        }
        

        $this->userModel->toggleStatus($id);
        $_SESSION['success'] = 'User status updated successfully.';

        $this->redirect('/admin/users');
    }

    public function getRecentActivity() {
        // This should be implemented based on your activity logging system
        // For now, returning dummy data
        return [
            ['action' => 'User Registration', 'user' => 'john.doe', 'details' => 'New user registered', 'date' => new \DateTime()],
            ['action' => 'Article Created', 'user' => 'jane.smith', 'details' => 'Created article "Getting Started"', 'date' => new \DateTime()],
        ];
    }
}
