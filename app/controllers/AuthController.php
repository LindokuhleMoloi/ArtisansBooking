<?php
class AuthController
{
    public function login(): void
    {
        if (Auth::check()) {
            header('Location: ' . APP_URL . '/index.php?page=dashboard');
            exit;
        }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            require_once APP_ROOT . '/app/models/User.php';
            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                Auth::login($user);
                header('Location: ' . APP_URL . '/index.php?page=dashboard');
                exit;
            }
            $error = 'Invalid email or password.';
        }

        $pageTitle = 'Login';
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/auth/login.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }

    public function register(): void
    {
        if (Auth::check()) {
            header('Location: ' . APP_URL . '/index.php?page=dashboard');
            exit;
        }

        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $role = $_POST['role'] ?? 'customer';
            if (!in_array($role, ['customer', 'artisan'], true)) {
                $role = 'customer';
            }

            $data = [
                'role'               => $role,
                'first_name'         => trim($_POST['first_name'] ?? ''),
                'last_name'          => trim($_POST['last_name'] ?? ''),
                'email'              => trim($_POST['email'] ?? ''),
                'phone'              => trim($_POST['phone'] ?? ''),
                'password'           => $_POST['password'] ?? '',
                'location_city'      => trim($_POST['location_city'] ?? ''),
                'location_province'  => trim($_POST['location_province'] ?? ''),
                'bio'                => trim($_POST['bio'] ?? ''),
            ];

            if (strlen($data['password']) < 6) {
                $error = 'Password must be at least 6 characters.';
            } else {
                require_once APP_ROOT . '/app/models/User.php';
                $userModel = new User();
                if ($userModel->findByEmail($data['email'])) {
                    $error = 'Email already registered.';
                } else {
                    $userId = $userModel->create($data);
                    if ($role === 'artisan') {
                        $db = Database::getInstance();
                        $db->prepare(
                            'INSERT INTO artisan_profiles (user_id, business_name, years_experience)
                             VALUES (?, ?, ?)'
                        )->execute([
                            $userId,
                            trim($_POST['business_name'] ?? $data['first_name'] . ' Services'),
                            (int)($_POST['years_experience'] ?? 0)
                        ]);
                    }
                    $success = $role === 'artisan'
                        ? 'Registration received. You can log in, and your profile will appear in the marketplace after admin approval.'
                        : 'Registration successful. You can now log in.';
                }
            }
        }

        $pageTitle = 'Register';
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/auth/register.php';
        require APP_ROOT . '/app/views/layouts/footer.php';
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: ' . APP_URL . '/index.php');
        exit;
    }
}
