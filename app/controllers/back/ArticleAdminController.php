<?php
namespace app\controllers\back;

use app\core\BaseController;
use app\models\Article;

class ArticleAdminController extends BaseController {
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
            $this->handleError($e);
            $this->view->render('back/articles/index.twig', [
                'error' => 'Failed to load articles',
                'articles' => []
            ]);
        }
    }

    public function create() {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $this->redirect('/login');
            return;
        }

        // Role check for admin access
        if ($_SESSION["user"]["role"] !== 'admin') {
            $this->redirect('/'); // Redirect to home or another appropriate page
            return;
        }

        try {
            $this->view->render('back/articles/create.twig');
        } catch (\Exception $e) {
            $this->handleError($e);
            $this->view->render('back/articles/create.twig');
        }
    }

    public function edit($id) {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $this->redirect('/login');
            return;
        }

        // Role check for admin access
        if ($_SESSION["user"]["role"] !== 'admin') {
            $this->redirect('/'); // Redirect to home or another appropriate page
            return;
        }

        // Logic for editing an article
    }

    public function delete($id) {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $this->redirect('/login');
            return;
        }

        // Role check for admin access
        if ($_SESSION["user"]["role"] !== 'admin') {
            $this->redirect('/'); // Redirect to home or another appropriate page
            return;
        }

        // Logic for deleting an article
    }

    public function store() {
        try {
            if (!isset($_POST['title']) || !isset($_POST['content'])) {
                throw new \Exception('Title and content are required');
            }

            $title = trim($_POST['title']);
            $content = trim($_POST['content']);

            if (empty($title) || empty($content)) {
                throw new \Exception('Title and content cannot be empty');
            }

            $this->articleModel->createArticle($title, $content);
            $this->redirect('/admin/articles');
        } catch (\Exception $e) {
            $this->handleError($e);
            $this->view->render('back/articles/create.twig', [
                'error' => 'Failed to create article',
                'old' => $_POST
            ]);
        }
    }
}