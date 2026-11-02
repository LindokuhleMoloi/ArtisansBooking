# Artisans Booking & Recommendations System

Pure PHP + MySQL application for booking local artisans/services and receiving personalised recommendations.

## Features

- User roles: Customer, Artisan, Admin
- Artisan profiles with ratings, verification, location
- Service catalogue by category
- Booking flow with slot conflict detection
- Hybrid recommendation engine (rating + popularity + category interest + location)
- Reviews & ratings that update artisan averages
- Responsive Bootstrap 5 UI

## Requirements

- PHP 8.0+
- MySQL 5.7+ / 8.0+ / MariaDB
- Apache/Nginx with `mod_rewrite` optional (front controller works without it)

## Installation (Shared Hosting)

1. Upload the entire `ArtisansBooking` folder to your hosting (e.g. `public_html/ArtisansBooking`).
2. Create a MySQL database and user.
3. Import `database/schema.sql` via phpMyAdmin or CLI.
4. Edit `config/config.php`:
   - Set `APP_URL` to your real URL (e.g. `https://yoursite.com/ArtisansBooking/public`)
   - Set `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
5. Point the web server document root (or a subdomain) at the `public/` folder if possible.
   - Alternative: access via `https://yoursite.com/ArtisansBooking/public/index.php`
6. Ensure `public/uploads/` is writable if you later add image uploads.

## Demo Accounts (after importing schema)

| Role     | Email                | Password   |
|----------|----------------------|------------|
| Customer | lerato@example.com   | password*  |
| Artisan  | thabo@example.com    | password*  |
| Artisan  | naledi@example.com   | password*  |
| Artisan  | sipho@example.com    | password*  |
| Admin    | admin@artisansrepublik.test | password* |

\* The seed uses a common bcrypt test hash. If login fails, register a new account or reset the password_hash with:

```php
echo password_hash('YourPassword', PASSWORD_DEFAULT);
```

## Project Structure

```
ArtisansBooking/
├── app/
│   ├── controllers/     # Request handlers, including administrator workflows
│   ├── models/          # Business logic & DB access
│   ├── views/           # PHP templates, including views/admin/
│   └── core/            # Database, Auth, Router
├── config/config.php
├── database/schema.sql
├── public/index.php     # Front controller (web root)
└── docs/                # Design documentation
```

### Existing database upgrade

If the database was created before artisan application review was added, run this once in phpMyAdmin or your MySQL client:

```sql
ALTER TABLE artisan_profiles
  ADD COLUMN approval_status ENUM('pending','approved','declined') NOT NULL DEFAULT 'pending';

UPDATE artisan_profiles
SET approval_status = CASE WHEN is_verified = 1 THEN 'approved' ELSE 'pending' END;
```

New artisan registrations start as pending. Administrators can review all applications from the **Admin** link after signing in.

## Recommendation Algorithm (summary)

Score =  
  0.40 × normalised rating +  
  0.25 × normalised total bookings +  
  0.20 × category match (from past bookings or selected preference) +  
  0.15 × location match (same city)  
+ small bonus for verified artisans.

Results are sorted by score descending.
