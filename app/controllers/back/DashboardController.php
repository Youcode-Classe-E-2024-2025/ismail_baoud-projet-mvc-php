<?php

class DashboardController {
    public function index() {
        // Render the dashboard view
        $view = new View();
        $view->render('back/dashboard.twig');
    }
}

?>
