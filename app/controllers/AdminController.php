<?php
class AdminController
{
    public function artisans(): void
    {
        Auth::requireRole('admin');
        require_once APP_ROOT . '/app/models/User.php';

        $allowedStatuses = ['all', 'pending', 'approved', 'declined'];
        $status = $_GET['status'] ?? 'all';
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? null)) {
                http_response_code(403);
                echo 'Invalid security token. Please reload the page and try again.';
                return;
            }

            $artisanId = filter_var($_POST['artisan_id'] ?? null, FILTER_VALIDATE_INT);
            $decision = $_POST['decision'] ?? '';
            if (!$artisanId || !in_array($decision, ['approved', 'declined'], true)) {
                http_response_code(400);
                echo 'Invalid artisan review request.';
                return;
            }

            if (!(new User())->updateArtisanApprovalStatus($artisanId, $decision)) {
                http_response_code(404);
                echo 'The artisan application was not found or has already been reviewed.';
                return;
            }

            header('Location: ' . APP_URL . '/index.php?page=admin-artisans&status=' . urlencode($status) . '&updated=' . urlencode($decision));
            exit;
        }

        $userModel = new User();
        $artisans = $userModel->getAllArtisans($status === 'all' ? null : $status);
        $stats = $userModel->getArtisanApprovalStats();
        $updated = $_GET['updated'] ?? '';
        $notice = in_array($updated, ['approved', 'declined'], true) ? $updated : null;
        $pageTitle = 'Artisan applications';

        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/admin/artisans.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }
}
