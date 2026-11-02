<?php
/**
 * Artisans Booking & Recommendations System
 * Configuration
 */

define('APP_NAME', 'Artisans Republik Bookings');
define('APP_URL', 'http://localhost/ArtisansBooking/public'); // Change to your domain
define('APP_ROOT', dirname(__DIR__));

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'artisans_booking');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Security
define('SESSION_NAME', 'artisans_session');
define('CSRF_TOKEN_NAME', 'csrf_token');

// Paths
define('UPLOAD_PATH', APP_ROOT . '/public/uploads/');
define('UPLOAD_URL', APP_URL . '/uploads/');

// Recommendation weights (tunable)
define('REC_WEIGHT_RATING', 0.40);
define('REC_WEIGHT_BOOKINGS', 0.25);
define('REC_WEIGHT_CATEGORY', 0.20);
define('REC_WEIGHT_LOCATION', 0.15);

// Timezone
date_default_timezone_set('Africa/Johannesburg');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
