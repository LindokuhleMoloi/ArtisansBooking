<?php
class RecommendationController
{
    public function index(): void
    {
        require_once APP_ROOT . '/app/models/RecommendationEngine.php';
        require_once APP_ROOT . '/app/models/Category.php';

        $categoryId = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        $engine = new RecommendationEngine();
        $recommendations = $engine->recommend(Auth::id(), 12, $categoryId);
        $categories = (new Category())->all();

        $pageTitle = 'Recommended for You';
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/customer/recommendations.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }
}
