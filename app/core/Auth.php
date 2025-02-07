<?php

namespace app\core;

use app\core\Controller;
use app\models\User;
use app\core\Session;
use app\core\Security;

use app\core\Validator;

class Auth extends Controller
{
    private $session;
    public function __construct()
    {
        parent::__construct();
        $this->session = new Session();
        $this->session->start();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator();
            $validator->validate($_POST, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $validator->getErrors();
                $this->view->render('auth/login.twig', [
                    'error' => $errors,
                    'old' => ['email' => $_POST['email'] ?? '']
                ]);
                return;
            }

            try {
                $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                if (!$email) {
                    Security::logSecurityIssue('Invalid email format for login attempt.');
                    throw new \Exception('Invalid email format');
                }

                $password = $_POST['password'];
                if (empty($password)) {
                    throw new \Exception('Password is required');
                }
                
                $userModel = new User();
                $user = $userModel->login($email, $password);
                if (!$user) {
                    Security::logSecurityIssue('Failed login attempt for email: ' . $email);
                    throw new \Exception('Invalid credentials');
                }
                
                // Store user data in session
                $this->session->setUser([
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]);
                
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
            $validator = new Validator();
            $validator->validate($_POST, [
                'username' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8',
                'confirm_password' => 'required|min:8',
            ]);

            if ($validator->fails()) {
                $errors = $validator->getErrors();
                $this->view->render('auth/register.twig', [
                    'error' => $errors,
                    'old' => [
                    'username' => $_POST['username'] ?? '',
                    'email' => $_POST['email'] ?? ''
                    ]
                ]);
                return;
            }

            try {
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
        $this->session->clearUserSession();
        $this->setFlashMessage('success', 'You have been successfully logged out.');
        $this->redirect('/login');
    }

    public function isLoggedIn()
    {
        return $this->session->getUser() !== null;
    }

    public function redirectToIntended($default)
    {
        $intended = $this->session->getIntendedUrl() ?? $default;
        $this->session->clearIntendedUrl();
        $this->redirect($intended);
    }

    protected function setFlashMessage($type, $message)
    {
        $this->session->setFlashMessage($type, $message);
    }
}