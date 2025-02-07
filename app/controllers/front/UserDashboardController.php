<?php
    namespace app\controllers\front;

use app\core\Controller;
use app\core\Validator;
use app\models\User;
use app\models\Article;
use app\models\Category;

class UserDashboardController extends Controller {
    private $userModel;
    private $articleModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->articleModel = new Article();
    }

    public function index() {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $this->redirect('/login');
            return;
        }

        // Role check
        if ($_SESSION["user"]["role"] !== 'user' && $_SESSION["user"]["role"] !== 'admin') {
            $this->redirect('/');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $userArticles = $this->articleModel->getArticlesByUser($userId, 1, 5);
        $totalArticles = $this->articleModel->getTotalArticlesByUser($userId);

        $this->view->render('dashboard/index.twig', [
            'articles' => $userArticles,
            'totalArticles' => $totalArticles
        ]);
    }

    public function createArticle() {
        if (!isset($_SESSION['user']['id'])) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get all categories for the dropdown
            $categoryModel = new Category();
            $categories = $categoryModel->getAllCategories();
            
            // Display the create article form
            $this->view->render('dashboard/create_article.twig', [
                'categories' => $categories
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator();
            $validator->validate($_POST, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required'
            ]);

            if ($validator->fails()) {
                $errors = $validator->getErrors();
                // Render the create article view with errors
                $this->view->render('dashboard/create_article.twig', [
                    'errors' => $errors,
                    'categories' => (new Category())->getAllCategories()
                ]);
                return;
            }

            $userId = $_SESSION['user']['id'];

            $data = [
                'title' => filter_input(INPUT_POST, 'title'),
                'content' => filter_input(INPUT_POST, 'content'),
                'category_id' => filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT),
                'user_id' => $userId,
                'status' => 'draft'
            ];

            try {
                if ($this->articleModel->create($data)) {
                    $this->setFlashMessage('success', 'Article created successfully');
                    $this->redirect('/dashboard');
                    return;
                }
                throw new \Exception('Failed to create article');
            } catch (\Exception $e) {
                $this->setFlashMessage('error', $e->getMessage());
                $this->redirect('/dashboard/create-article');
            }
        }
    }

    private function setFlashMessage($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}
