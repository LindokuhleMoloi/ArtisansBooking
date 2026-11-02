<?php
class ReviewController
{
    public function create(): void
    {
        Auth::requireLogin();
        require_once APP_ROOT . '/app/models/Booking.php';
        require_once APP_ROOT . '/app/models/Review.php';

        $bookingId = (int)($_GET['booking_id'] ?? $_POST['booking_id'] ?? 0);
        $bookingModel = new Booking();
        $booking = $bookingModel->findById($bookingId);

        if (!$booking || (int)$booking['customer_id'] !== Auth::id()) {
            http_response_code(403);
            echo 'Invalid booking';
            return;
        }
        if ($booking['status'] !== 'completed') {
            echo 'You can only review completed bookings.';
            return;
        }

        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rating = (int)($_POST['rating'] ?? 0);
            $comment = trim($_POST['comment'] ?? '');
            if ($rating < 1 || $rating > 5) {
                $error = 'Please select a rating between 1 and 5.';
            } else {
                try {
                    (new Review())->create([
                        'booking_id'  => $bookingId,
                        'customer_id' => Auth::id(),
                        'artisan_id'  => $booking['artisan_id'],
                        'rating'      => $rating,
                        'comment'     => $comment,
                    ]);
                    $success = 'Thank you for your review!';
                } catch (PDOException $e) {
                    $error = 'You have already reviewed this booking.';
                }
            }
        }

        $pageTitle = 'Leave a Review';
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/customer/review.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }
}
