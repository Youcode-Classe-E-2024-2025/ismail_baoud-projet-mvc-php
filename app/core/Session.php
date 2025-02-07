<?php

namespace app\core;

class session{
    
    public static function start(){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
    }
    public static function set($key, $value){
        $_SESSION[$key] = $value;
    }
    public static function get($key){
        return $_SESSION[$key];
    }
    public static function setUser($user){
        $_SESSION['user'] = $user;
    }
    public static function getUser(){
        return $_SESSION['user'];
    }
    public static function clearUserSession() {
        unset($_SESSION['user']);
    }
    public static function setFlashMessage($type, $message) {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}
?>