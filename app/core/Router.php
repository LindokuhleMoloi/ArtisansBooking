<?php
/**
 * Very simple front-controller style router
 */
class Router
{
    public static function dispatch(): void
    {
        $page = $_GET['page'] ?? 'home';
        $action = $_GET['action'] ?? 'index';

        $map = [
            'home'          => ['HomeController', 'index'],
            'login'         => ['AuthController', 'login'],
            'register'      => ['AuthController', 'register'],
            'logout'        => ['AuthController', 'logout'],
            'artisans'      => ['ArtisanController', 'list'],
            'artisan'       => ['ArtisanController', 'show'],
            'book'          => ['BookingController', 'create'],
            'my-bookings'   => ['BookingController', 'myBookings'],
            'manage-bookings' => ['BookingController', 'manage'],
            'recommendations' => ['RecommendationController', 'index'],
            'dashboard'     => ['DashboardController', 'index'],
            'admin-artisans' => ['AdminController', 'artisans'],
            'profile'       => ['ProfileController', 'index'],
            'services'      => ['ServiceController', 'index'],
            'review'        => ['ReviewController', 'create'],
        ];

        if (!isset($map[$page])) {
            http_response_code(404);
            echo 'Page not found';
            return;
        }

        [$controllerName, $method] = $map[$page];
        $controllerFile = APP_ROOT . '/app/controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            http_response_code(500);
            echo 'Controller missing';
            return;
        }

        require_once $controllerFile;
        $controller = new $controllerName();
        if (!method_exists($controller, $method)) {
            http_response_code(404);
            echo 'Action not found';
            return;
        }
        $controller->$method();
    }
}
