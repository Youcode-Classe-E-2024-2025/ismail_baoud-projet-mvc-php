<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Article;
use app\models\Category;

class ArticleController extends Controller {
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

        $this->view->render('articles/index.twig', [
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

        $this->view->render('articles/show.twig', [
            'article' => $article
        ]);
    }
}
