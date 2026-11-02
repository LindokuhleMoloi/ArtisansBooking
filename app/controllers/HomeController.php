<?php
class HomeController
{
    public function index(): void
    {
        require_once APP_ROOT . '/app/models/RecommendationEngine.php';
        require_once APP_ROOT . '/app/models/Category.php';
        require_once APP_ROOT . '/app/models/Service.php';

        $engine = new RecommendationEngine();
        $userId = Auth::id();
        $recommendations = $engine->recommend($userId, 6);

        $categories = (new Category())->all();
        $services = (new Service())->getAllActive();

        $pageTitle = 'Home';
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/home.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }
}
