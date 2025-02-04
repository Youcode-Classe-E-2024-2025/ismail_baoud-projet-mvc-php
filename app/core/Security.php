<?php

namespace app\core;

class Security {
    public function sanitize($input) {
        // Logic for sanitizing input to prevent XSS
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}

?>
