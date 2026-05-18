<?php
// controllers/AuthController.php
// Handles: Register, Login, Logout
// NOTE: session_start() is NOT here — already called in public/index.php
// NOTE: BASE constant is defined in public/index.php — used for all redirects

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{

    private $userModel;

    public function __construct()
    {
        $db = getDB();
        $this->userModel = new UserModel($db);
    }

    // ── Show registration form (GET request) ──
    public function showRegister()
    {
        $errors = [];
        require_once __DIR__ . '/../views/auth/register.php';
    }

    // ── Process registration form (POST request) ──
    public function register()
    {
        $errors = [];

        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = trim($_POST['role']     ?? '');

        // Validate every field
        if (empty($name))
            $errors['name'] = 'Name is required.';

        if (empty($email))
            $errors['email'] = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'Enter a valid email address.';

        if (empty($password))
            $errors['password'] = 'Password is required.';
        elseif (strlen($password) < 8)
            $errors['password'] = 'Password must be at least 8 characters.';

        if (!in_array($role, ['student', 'instructor']))
            $errors['role'] = 'Please select a role.';

        // Check email is not already taken
        if (empty($errors['email']) && $this->userModel->findByEmail($email)) {
            $errors['email'] = 'That email address is already registered.';
        }

        // If any errors — reload form and show them
        if (!empty($errors)) {
            require_once __DIR__ . '/../views/auth/register.php';
            return;
        }

        // Hash password before saving — NEVER store plain text
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $isActive = ($role === 'instructor') ? 0 : 1;
        $this->userModel->createUser($name, $email, $password_hash, $role, $isActive);

        // Redirect to login with success flag
        header('Location: ' . BASE . '?page=login&registered=1');
        exit;
    }

    // ── Show login form (GET request) ──
    public function showLogin()
    {
        $error          = '';
        $justRegistered = isset($_GET['registered']);
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // ── Process login form (POST request) ──
    public function login()
    {
        $error          = '';
        $justRegistered = false;

        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        // Find user by email
        $user = $this->userModel->findByEmail($email);

        // Check password matches stored hash
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Invalid email or password.';
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        // Check account is not suspended
        if ($user['is_active'] == 0) {
            $error = $user['role'] === 'instructor'
                ? 'Your account is pending admin approval. Please wait.'
                : 'Your account has been suspended.';
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        // Save user info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];

        // Redirect to the correct dashboard based on role
        $destinations = [
            'student'    => BASE . '?page=student/home',
            'instructor' => BASE . '?page=instructor/home',
            'admin'      => BASE . '?page=admin/panel',
        ];

        header('Location: ' . ($destinations[$user['role']] ?? BASE . '?page=login'));
        exit;
    }

    // ── Logout — destroy session and redirect to login ──
    public function logout()
    {
        session_destroy();
        header('Location: ' . BASE . '?page=login');
        exit;
    }
}
