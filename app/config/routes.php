<?php

use app\controllers\HomeController;
use app\controllers\ArticleController;
use app\controllers\AuthController;
use app\controllers\DashboardController;
use app\controllers\back\AdminDashboardController;
use app\controllers\front\UserDashboardController;

return [
    // Public routes
    '' => ['app\controllers\HomeController', 'index'],
    'articles' => ['app\controllers\ArticleController', 'index'],
    'articles/{id}' => ['app\controllers\ArticleController', 'show'],
    'login' => ['app\core\Auth', 'login'],
    'register' => ['app\core\Auth', 'register'],
    'logout' => ['app\core\Auth', 'logout'],
    
    // User dashboard routes
    'dashboard' => ['app\controllers\front\UserDashboardController', 'index'],
    'dashboard/create-article' => ['app\controllers\front\UserDashboardController', 'createArticle'],
    'dashboard/articles/edit/{id}' => ['app\controllers\front\UserDashboardController', 'editArticle'],
    'dashboard/articles/delete/{id}' => ['app\controllers\front\UserDashboardController', 'deleteArticle'],
    
    // Admin routes
    'admin/dashboard' => ['app\controllers\back\AdminDashboardController', 'index'],
    'admin/users' => ['app\controllers\back\AdminDashboardController', 'users'],
    'admin/articles' => ['app\controllers\back\AdminDashboardController', 'articles'],
    'admin/users/{id}/toggle-status' => ['app\controllers\back\AdminDashboardController', 'toggleUserStatus'],
    'admin/articles/{id}/delete' => ['app\controllers\back\ArticleAdminController', 'delete'],
    '/articles/{id}/edit' => ['app\controllers\back\ArticleAdminController', 'edit'],
    '/admin/articles/{id}/update' => ['app\controllers\back\ArticleAdminController', 'update']

];