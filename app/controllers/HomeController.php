<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Article;
use app\models\Category;

class HomeController extends Controller {
    private $articleModel;
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->articleModel = new Article();
        $this->categoryModel = new Category();
    }

    public function index() {
        $latestArticles = $this->articleModel->getLatestArticles(6);
        $categories = $this->categoryModel->getAllCategories();

        $this->view->render('home/index.twig', [
            'articles' => $latestArticles,
            'categories' => $categories
        ]);
    }
}
