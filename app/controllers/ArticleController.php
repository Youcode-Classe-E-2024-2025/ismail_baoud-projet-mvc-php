<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Article;
use app\models\Category;
use Twig\Environment;

class ArticleController extends BaseController {
    private $articleModel;
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->articleModel = new Article();
        $this->categoryModel = new Category();
    }



    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        
        $articles = $this->articleModel->getAllArticles($page, $limit);
        $totalArticles = $this->articleModel->getTotalArticles();
        $totalPages = ceil($totalArticles / $limit);

        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
        $twig = new \Twig\Environment($loader);
        echo $twig->render('articles/index.twig', [
            'articles' => $articles,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function show($id) {
        $article = $this->articleModel->getArticleById($id);
        
        if (!$article) {
            $this->setFlashMessage('error', 'Article not found');
            $this->redirect('/articles');
            return;
        }

        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
        $twig = new \Twig\Environment($loader);
        echo $twig->render('articles/show.twig', ['article' => $article]);
    }

    public function edit($id) {
        try {
            $article = $this->articleModel->getArticleById($id);
            if (!$article) {
                throw new \Exception('Article not found');
            }
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
            $twig = new \Twig\Environment($loader);
            echo $twig->render('articles/edit.twig', ['article' => $article]);
        } catch (\Exception $e) {
            $this->handleError($e);
            $this->redirect('dashboard');
        }
    }

    public function update($id) {
        // Check if user is logged in
        if (!isset($_SESSION['user']['id'])) {
            header('Location: /login');
            exit;
        }

        try {
            if (!isset($_POST['title']) || !isset($_POST['content'])) {
                throw new \Exception('Title and content are required');
            }

            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $stmt = [
                'id' => $id,
                'title' => $title,
                'content' => $content
            ];

            if (empty($title) || empty($content)) {
                throw new \Exception('Title and content cannot be empty');
            }

            $this->articleModel->updateArticle($stmt);
            $this->logMessage("Article updated with ID: $id and data: " . json_encode($stmt));
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
            $twig = new \Twig\Environment($loader);
            $this->redirect('/admin/articles');
        } catch (\Exception $e) {
            $this->handleError($e);
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
            $twig = new \Twig\Environment($loader);
            echo $twig->render('articles/edit.twig');
        }
    }

    public function delete($id) {
        try {
            $this->articleModel->deleteArticle($id);
            $this->logMessage("Article deleted with ID: $id");
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
            $twig = new \Twig\Environment($loader);
            echo $twig->render('articles/index.twig');
        } catch (\Exception $e) {
            $this->handleError($e);
            $this->logMessage("Error deleting article ID: $id - " . $e->getMessage());
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
            $twig = new \Twig\Environment($loader);
            echo $twig->render('articles/index.twig');
        }
    }
}
