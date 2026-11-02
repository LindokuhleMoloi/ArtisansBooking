<?php
class ArtisanController
{
    public function list(): void
    {
        require_once APP_ROOT . '/app/models/User.php';
        require_once APP_ROOT . '/app/models/Category.php';

        $filters = [
            'city'        => trim($_GET['city'] ?? ''),
            'category_id' => !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null,
            'min_rating'  => !empty($_GET['min_rating']) ? (float)$_GET['min_rating'] : null,
        ];

        $artisans = (new User())->getArtisans($filters);
        $categories = (new Category())->all();

        $pageTitle = 'Find Artisans';
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/customer/artisans.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }

    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        require_once APP_ROOT . '/app/models/User.php';
        require_once APP_ROOT . '/app/models/Service.php';
        require_once APP_ROOT . '/app/models/Review.php';

        $userModel = new User();
        $artisan = $userModel->findById($id);
        if (!$artisan || $artisan['role'] !== 'artisan') {
            http_response_code(404);
            echo 'Artisan not found';
            return;
        }

        // Load profile extras
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM artisan_profiles WHERE user_id = ?');
        $stmt->execute([$id]);
        $profile = $stmt->fetch() ?: [];
        if (($profile['approval_status'] ?? null) !== 'approved') {
            http_response_code(404);
            echo 'Artisan not found';
            return;
        }

        $services = (new Service())->getByArtisan($id);
        $reviews  = (new Review())->getByArtisan($id);

        $pageTitle = $artisan['first_name'] . ' ' . $artisan['last_name'];
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/customer/artisan_detail.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }
}
