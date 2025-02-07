<?php

namespace app\core;

use app\core\Controller;
use app\models\User;
use app\services\PasswordValidator;

class Auth extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->initSession();
    }

    private function initSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!isset($_POST['email']) || !isset($_POST['password'])) {
                    throw new \Exception('Email and password are required');
                }

                $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                if (!$email) {
                    throw new \Exception('Invalid email format');
                }

                $password = $_POST['password'];
                if (empty($password)) {
                    throw new \Exception('Password is required');
                }
                
                $userModel = new User();
                $user = $userModel->login($email, $password);
                
                // Store user data in session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: /admin/dashboard');
                    exit;
                } else {
                    header('Location: /dashboard');
                    exit;
                }
                
            } catch (\Exception $e) {
                $this->view->render('auth/login.twig', [
                    'error' => $e->getMessage(),
                    'old' => ['email' => $_POST['email'] ?? '']
                ]);
                return;
            }
        }
        
        $this->view->render('auth/login.twig');
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!isset($_POST['username']) || !isset($_POST['email']) || 
                    !isset($_POST['password']) || !isset($_POST['confirm_password'])) {
                    throw new \Exception('All fields are required');
                }

                $username = trim($_POST['username']);
                $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                $password = $_POST['password'];
                $confirmPassword = $_POST['confirm_password'];
                
                if (!$email) {
                    throw new \Exception('Invalid email format');
                }
                
                $this->validateRegistration($username, $email, $password, $confirmPassword);
                
                $userModel = new User();
                $success = $userModel->register($username, $email, $password);
                
                if ($success) {
                    $this->setFlashMessage('success', 'Registration successful! Please login.');
                    $this->redirect('/login');
                }
            } catch (\Exception $e) {
                $this->view->render('auth/register.twig', [
                    'error' => $e->getMessage(),
                    'old' => [
                        'username' => $_POST['username'] ?? '',
                        'email' => $_POST['email'] ?? ''
                    ]
                ]);
                return;
            }
        }
        
        $this->view->render('auth/register.twig');
    }

    public function validateRegistration($username, $email, $password, $confirmPassword)
    {
        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            throw new \Exception('All fields are required');
        }

        if ($password !== $confirmPassword) {
            throw new \Exception('Passwords do not match');
        }

        if (strlen($password) < 8) {
            throw new \Exception('Password must be at least 8 characters long');
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new \Exception('Password must contain at least one uppercase letter');
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new \Exception('Password must contain at least one lowercase letter');
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new \Exception('Password must contain at least one number');
        }

        if (!preg_match('/[\W]/', $password)) {
            throw new \Exception('Password must contain at least one special character');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email format');
        }
    }

    public function logout()
    {
        $this->clearUserSession();
        $this->setFlashMessage('success', 'You have been successfully logged out.');
        $this->redirect('/login');
    }

    private function setUserSession($user)
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['last_activity'] = time();
    }

    private function clearUserSession()
    {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function redirectToIntended($default)
    {
        $intended = $_SESSION['intended_url'] ?? $default;
        unset($_SESSION['intended_url']);
        $this->redirect($intended);
    }

    protected function setFlashMessage($type, $message)
    {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}