<?php
class HomeController {
    public function index() {
        // Render the home view
        $view = new View();
        $view->render('front/home.twig');
    }
}

?>
