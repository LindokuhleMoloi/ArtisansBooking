<?php
class BookingController
{
    public function create(): void
    {
        Auth::requireLogin();
        if (!Auth::is('customer') && !Auth::is('admin')) {
            // Allow artisan to book as well if needed, but primary is customer
        }

        require_once APP_ROOT . '/app/models/Service.php';
        require_once APP_ROOT . '/app/models/Booking.php';

        $serviceId = (int)($_GET['service_id'] ?? $_POST['service_id'] ?? 0);
        $serviceModel = new Service();
        $service = $serviceModel->findById($serviceId);

        if (!$service) {
            http_response_code(404);
            echo 'Service not found';
            return;
        }

        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date  = $_POST['booking_date'] ?? '';
            $start = $_POST['start_time'] ?? '';
            $notes = trim($_POST['notes'] ?? '');
            $address = trim($_POST['address'] ?? '');

            $duration = (int)$service['duration_minutes'];
            $endTs = strtotime($date . ' ' . $start) + ($duration * 60);
            $end = date('H:i:s', $endTs);

            $bookingModel = new Booking();
            if (!$bookingModel->isSlotAvailable((int)$service['artisan_id'], $date, $start . ':00', $end)) {
                $error = 'This time slot is no longer available. Please choose another.';
            } else {
                $bookingId = $bookingModel->create([
                    'customer_id'  => Auth::id(),
                    'artisan_id'   => $service['artisan_id'],
                    'service_id'   => $serviceId,
                    'booking_date' => $date,
                    'start_time'   => $start . ':00',
                    'end_time'     => $end,
                    'total_price'  => $service['price'],
                    'notes'        => $notes,
                    'address'      => $address,
                    'status'       => 'pending',
                ]);
                $success = 'Booking request submitted successfully! Booking #' . $bookingId;
            }
        }

        $pageTitle = 'Book Service';
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/customer/book.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }

    public function myBookings(): void
    {
        Auth::requireLogin();
        require_once APP_ROOT . '/app/models/Booking.php';
        $bookings = (new Booking())->getByCustomer(Auth::id());

        $pageTitle = 'My Bookings';
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/customer/my_bookings.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }

    public function manage(): void
    {
        Auth::requireLogin();
        if (!Auth::is('artisan') && !Auth::is('admin')) {
            http_response_code(403);
            echo 'Access denied';
            return;
        }

        require_once APP_ROOT . '/app/models/Booking.php';
        $bookingModel = new Booking();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
            $bookingModel->updateStatus((int)$_POST['booking_id'], $_POST['status']);
        }

        $bookings = $bookingModel->getByArtisan(Auth::id());

        $pageTitle = 'Manage Bookings';
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/artisan/manage_bookings.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }
}
