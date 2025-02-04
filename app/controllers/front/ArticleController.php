<?php

class ArticleController {
    public function show() {
        // Render the article view
        $view = new View();
        $view->render('front/article.twig');
    }
}

?>
