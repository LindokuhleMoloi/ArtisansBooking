-- ============================================================
-- Artisans Booking & Recommendations System
-- MySQL Schema (PHP 8 + MySQL 5.7+/8 compatible)
-- ============================================================

CREATE DATABASE IF NOT EXISTS artisans_booking
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE artisans_booking;

-- ------------------------------------------------------------
-- Users (customers, artisans, admin)
-- ------------------------------------------------------------
CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role            ENUM('customer','artisan','admin') NOT NULL DEFAULT 'customer',
    first_name      VARCHAR(80)  NOT NULL,
    last_name       VARCHAR(80)  NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    phone           VARCHAR(30)  NULL,
    password_hash   VARCHAR(255) NOT NULL,
    location_city   VARCHAR(100) NULL,
    location_province VARCHAR(100) NULL,
    bio             TEXT NULL,
    profile_image   VARCHAR(255) NULL,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_city (location_city)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Categories (e.g. Plumbing, Carpentry, Beauty, etc.)
-- ------------------------------------------------------------
CREATE TABLE categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    slug        VARCHAR(120) NOT NULL UNIQUE,
    description TEXT NULL,
    icon        VARCHAR(50) NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Artisan profiles (extra data for role=artisan)
-- ------------------------------------------------------------
CREATE TABLE artisan_profiles (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             INT UNSIGNED NOT NULL UNIQUE,
    business_name       VARCHAR(150) NULL,
    years_experience    TINYINT UNSIGNED DEFAULT 0,
    service_radius_km   SMALLINT UNSIGNED DEFAULT 20,
    avg_rating          DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    total_reviews       INT UNSIGNED NOT NULL DEFAULT 0,
    total_bookings      INT UNSIGNED NOT NULL DEFAULT 0,
    is_verified         TINYINT(1) NOT NULL DEFAULT 0,
    approval_status     ENUM('pending','approved','declined') NOT NULL DEFAULT 'pending',
    availability_note   VARCHAR(255) NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ap_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Services offered by artisans
-- ------------------------------------------------------------
CREATE TABLE services (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    artisan_id      INT UNSIGNED NOT NULL,
    category_id     INT UNSIGNED NOT NULL,
    title           VARCHAR(150) NOT NULL,
    description     TEXT NULL,
    price           DECIMAL(10,2) NOT NULL,
    duration_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 60,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_svc_artisan FOREIGN KEY (artisan_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_svc_category FOREIGN KEY (category_id) REFERENCES categories(id),
    INDEX idx_artisan (artisan_id),
    INDEX idx_category (category_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Availability slots (simple weekly or specific dates)
-- ------------------------------------------------------------
CREATE TABLE availability_slots (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    artisan_id      INT UNSIGNED NOT NULL,
    day_of_week     TINYINT UNSIGNED NULL COMMENT '0=Sun ... 6=Sat, null = specific date',
    specific_date   DATE NULL,
    start_time      TIME NOT NULL,
    end_time        TIME NOT NULL,
    is_available    TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_slot_artisan FOREIGN KEY (artisan_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_artisan_date (artisan_id, specific_date),
    INDEX idx_artisan_dow (artisan_id, day_of_week)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Bookings
-- ------------------------------------------------------------
CREATE TABLE bookings (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id     INT UNSIGNED NOT NULL,
    artisan_id      INT UNSIGNED NOT NULL,
    service_id      INT UNSIGNED NOT NULL,
    booking_date    DATE NOT NULL,
    start_time      TIME NOT NULL,
    end_time        TIME NOT NULL,
    status          ENUM('pending','confirmed','completed','cancelled','no_show') NOT NULL DEFAULT 'pending',
    total_price     DECIMAL(10,2) NOT NULL,
    notes           TEXT NULL,
    address         VARCHAR(255) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_book_customer FOREIGN KEY (customer_id) REFERENCES users(id),
    CONSTRAINT fk_book_artisan  FOREIGN KEY (artisan_id)  REFERENCES users(id),
    CONSTRAINT fk_book_service  FOREIGN KEY (service_id)  REFERENCES services(id),
    INDEX idx_customer (customer_id),
    INDEX idx_artisan_date (artisan_id, booking_date),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Reviews & Ratings
-- ------------------------------------------------------------
CREATE TABLE reviews (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id      INT UNSIGNED NOT NULL UNIQUE,
    customer_id     INT UNSIGNED NOT NULL,
    artisan_id      INT UNSIGNED NOT NULL,
    rating          TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment         TEXT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rev_booking  FOREIGN KEY (booking_id)  REFERENCES bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_rev_customer FOREIGN KEY (customer_id) REFERENCES users(id),
    CONSTRAINT fk_rev_artisan  FOREIGN KEY (artisan_id)  REFERENCES users(id),
    INDEX idx_artisan (artisan_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Recommendation log (optional analytics)
-- ------------------------------------------------------------
CREATE TABLE recommendation_logs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NULL,
    artisan_id      INT UNSIGNED NOT NULL,
    score           DECIMAL(6,4) NOT NULL,
    reason          VARCHAR(100) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- ============================================================
-- Seed data
-- ============================================================

INSERT INTO categories (name, slug, description, icon) VALUES
('Plumbing',      'plumbing',      'Pipes, leaks, installations', 'droplet'),
('Electrical',    'electrical',    'Wiring, lights, faults', 'lightning'),
('Carpentry',     'carpentry',     'Furniture, fittings, woodwork', 'hammer'),
('Painting',      'painting',      'Interior & exterior painting', 'brush'),
('Beauty & Hair', 'beauty-hair',   'Hair, nails, makeup', 'sparkles'),
('Cleaning',      'cleaning',      'Home and office cleaning', 'sparkle'),
('Gardening',     'gardening',     'Lawn, plants, landscaping', 'leaf'),
('Tutoring',      'tutoring',      'Academic and skills tutoring', 'book');

-- Admin account (password: Admin@123)
INSERT INTO users (role, first_name, last_name, email, phone, password_hash, location_city, location_province) VALUES
('admin', 'System', 'Admin', 'admin@artisansrepublik.test', '0800000000',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Johannesburg', 'Gauteng');

-- Sample artisans (password for all demo users: Password1!)
-- Hash generated for "Password1!"
INSERT INTO users (role, first_name, last_name, email, phone, password_hash, location_city, location_province, bio) VALUES
('artisan', 'Thabo', 'Mokoena', 'thabo@example.com', '0821110001',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Benoni', 'Gauteng',
 'Experienced plumber with 8 years in residential and commercial work.'),
('artisan', 'Naledi', 'Dlamini', 'naledi@example.com', '0821110002',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pretoria', 'Gauteng',
 'Professional hair stylist and beauty therapist.'),
('artisan', 'Sipho', 'Nkosi', 'sipho@example.com', '0821110003',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Johannesburg', 'Gauteng',
 'Qualified electrician, installations and fault finding.'),
('customer', 'Lerato', 'Molefe', 'lerato@example.com', '0832220001',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Benoni', 'Gauteng', NULL);

INSERT INTO artisan_profiles (user_id, business_name, years_experience, service_radius_km, avg_rating, total_reviews, total_bookings, is_verified, approval_status) VALUES
(2, 'Thabo Plumbing Solutions', 8, 30, 4.70, 12, 45, 1, 'approved'),
(3, 'Naledi Beauty Studio', 5, 15, 4.90, 28, 90, 1, 'approved'),
(4, 'Sipho Electrical Services', 6, 25, 4.50, 9, 32, 1, 'approved');

INSERT INTO services (artisan_id, category_id, title, description, price, duration_minutes) VALUES
(2, 1, 'Leak Repair', 'Find and fix household leaks', 350.00, 60),
(2, 1, 'Geyser Installation', 'Full geyser replacement', 1800.00, 180),
(3, 5, 'Ladies Haircut & Style', 'Cut, wash and blow-dry', 280.00, 90),
(3, 5, 'Gel Manicure', 'Full set gel nails', 220.00, 75),
(4, 2, 'Socket & Switch Repair', 'Faulty plug points and switches', 400.00, 45),
(4, 2, 'DB Board Inspection', 'Safety check of distribution board', 650.00, 90);

-- Simple weekly availability (Mon-Fri 08:00-17:00) for artisans
INSERT INTO availability_slots (artisan_id, day_of_week, start_time, end_time) VALUES
(2, 1, '08:00:00', '17:00:00'),
(2, 2, '08:00:00', '17:00:00'),
(2, 3, '08:00:00', '17:00:00'),
(2, 4, '08:00:00', '17:00:00'),
(2, 5, '08:00:00', '17:00:00'),
(3, 1, '09:00:00', '18:00:00'),
(3, 2, '09:00:00', '18:00:00'),
(3, 3, '09:00:00', '18:00:00'),
(3, 4, '09:00:00', '18:00:00'),
(3, 5, '09:00:00', '18:00:00'),
(3, 6, '09:00:00', '14:00:00'),
(4, 1, '07:30:00', '16:30:00'),
(4, 2, '07:30:00', '16:30:00'),
(4, 3, '07:30:00', '16:30:00'),
(4, 4, '07:30:00', '16:30:00'),
(4, 5, '07:30:00', '16:30:00');
