<?php

namespace app\core;

class Security {
    public static function logSecurityIssue($message) {
        if (!$email) {
            Security::logSecurityIssue('Invalid email format for login attempt.');
            throw new \Exception('Invalid email format');
        }
        
        // Attempt to log in the user
        $user = $userModel->login($email, $password);
        if (!$user) {
            Security::logSecurityIssue('Failed login attempt for email: ' . $email);
            throw new \Exception('Invalid credentials');
        }
    }
}
?>