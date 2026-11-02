<?php
class DashboardController
{
    public function index(): void
    {
        Auth::requireLogin();
        $role = Auth::role();

        if ($role === 'admin') {
            header('Location: ' . APP_URL . '/index.php?page=admin-artisans');
            exit;
        }

        require_once APP_ROOT . '/app/models/Booking.php';
        require_once APP_ROOT . '/app/models/RecommendationEngine.php';

        $bookingModel = new Booking();
        $data = [];

        if ($role === 'customer') {
            $data['bookings'] = $bookingModel->getByCustomer(Auth::id());
            $data['recommendations'] = (new RecommendationEngine())->recommend(Auth::id(), 4);
        } elseif ($role === 'artisan') {
            $data['bookings'] = $bookingModel->getByArtisan(Auth::id());
            $data['pending'] = array_filter($data['bookings'], fn($b) => $b['status'] === 'pending');
            require_once APP_ROOT . '/app/models/User.php';
            $data['approval_status'] = (new User())->getArtisanApprovalStatus(Auth::id());
        }

        $pageTitle = 'Dashboard';
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/dashboard.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }
}
