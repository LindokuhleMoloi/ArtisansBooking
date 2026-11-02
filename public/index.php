<?php
/**
 * Front Controller
 * Artisans Booking & Recommendations System
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once APP_ROOT . '/app/core/Database.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/core/Router.php';

Auth::start();
Router::dispatch();
