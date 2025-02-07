<?php
namespace app\controllers\back;

use app\core\Controller;
use app\models\Article;

class ArticleAdminController extends Controller {
    private $articleModel;

    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION['user']['id']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        $this->articleModel = new Article();
    }

    public function index() {
        try {
            $articles = $this->articleModel->getAllArticles();
            $this->view->render('back/articles/index.twig', ['articles' => $articles]);
        } catch (\Exception $e) {
            $this->view->render('back/articles/index.twig', [
                'error' => 'Failed to load articles: ' . $e->getMessage(),
                'articles' => []
            ]);
        }
    }

    public function create() {
        $this->view->render('back/articles/create.twig');
    }

    // public function store() {
    //     try {
    //         if (!isset($_POST['title']) || !isset($_POST['content'])) {
    //             throw new \Exception('Title and content are required');
    //         }

    //         $title = trim($_POST['title']);
    //         $content = trim($_POST['content']);

    //         if (empty($title) || empty($content)) {
    //             throw new \Exception('Title and content cannot be empty');
    //         }

    //         $this->articleModel->createArticle($title, $content);
    //         $this->redirect('/admin/articles');
    //     } catch (\Exception $e) {
    //         $this->view->render('back/articles/create.twig', [
    //             'error' => $e->getMessage(),
    //             'old' => $_POST
    //         ]);
    //     }
    // }

    public function edit($id) {
        try {
            $article = $this->articleModel->getArticleById($id);
            if (!$article) {
                throw new \Exception('Article not found');
            }
            $this->view->render('/articles/edit.twig', ['article' => $article]);
        } catch (\Exception $e) {
            $this->redirect('/admin/articles');
        }
    }

    public function update($id) {
        try {
            if (!isset($_POST['title']) || !isset($_POST['content'])) {
                throw new \Exception('Title and content are required');
            }

            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $stmt= [
                'id' => $id,
                'title' => $title,
                'content' => $content
            ];
            if (empty($title) || empty($content)) {
                throw new \Exception('Title and content cannot be empty');
            }
            
            $hi = $this->articleModel->updateArticle($stmt);
            $this->redirect('/admin/articles');
            var_dump($hi);
            die();
        } catch (\Exception $e) {
            $this->view->render('articles/edit.twig');
            var_dump([
                'error' => $e->getMessage(),
                'article' => [
                    'id' => $id,
                    'title' => $_POST['title'] ?? '',
                    'content' => $_POST['content'] ?? ''
                ]
            ]);
        }
    }

    public function delete($id) {
        try {
            $this->articleModel->deleteArticle($id);
            $this->redirect('/admin/articles');
        } catch (\Exception $e) {
            $this->redirect('/admin/articles');
        }
    }

}