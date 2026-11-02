<?php
/**
 * Session-based Authentication Helper
 */
class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
    }

    public static function login(array $user): void
    {
        self::start();
        $_SESSION['user'] = [
            'id'         => (int)$user['id'],
            'role'       => $user['role'],
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
            'email'      => $user['email'],
        ];
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        self::start();
        return isset($_SESSION['user']['id']);
    }

    public static function user(): ?array
    {
        self::start();
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        $u = self::user();
        return $u ? (int)$u['id'] : null;
    }

    public static function role(): ?string
    {
        $u = self::user();
        return $u['role'] ?? null;
    }

    public static function is(string $role): bool
    {
        return self::role() === $role;
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . APP_URL . '/index.php?page=login');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();
        if (!self::is($role)) {
            http_response_code(403);
            echo 'Access denied.';
            exit;
        }
    }

    public static function csrfToken(): string
    {
        self::start();
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    public static function validateCsrfToken(mixed $token): bool
    {
        self::start();
        $sessionToken = $_SESSION[CSRF_TOKEN_NAME] ?? null;
        return is_string($token)
            && is_string($sessionToken)
            && hash_equals($sessionToken, $token);
    }
}
