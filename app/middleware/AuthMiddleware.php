<?php

namespace app\middleware;

use app\models\User;

class AuthMiddleware {
    public function handleRequest() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check session timeout (30 minutes)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            $this->clearSession();
            header('Location: /login?message=' . urlencode('Session expired. Please login again.'));
            exit();
        }

        // Update last activity time
        $_SESSION['last_activity'] = time();

        // Check if IP changed (potential session hijacking)
        if (isset($_SESSION['user_ip']) && $_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
            $this->clearSession();
            header('Location: /login?message=' . urlencode('Security alert: Session invalidated.'));
            exit();
        }

        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Public routes that don't require authentication
        $publicRoutes = ['/login', '/register', '/forgot-password', '/reset-password'];
        if (in_array($currentPath, $publicRoutes)) {
            return true;
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            header('Location: /login');
            exit();
        }

        // Admin routes check
        if (strpos($currentPath, '/admin') === 0) {
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== User::ROLE_ADMIN) {
                header('Location: /dashboard?error=' . urlencode('Access denied. Admin privileges required.'));
                exit();
            }
        }

        return true;
    }

    private function clearSession() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }
}
