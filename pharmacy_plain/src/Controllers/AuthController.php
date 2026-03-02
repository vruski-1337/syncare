<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;

final class AuthController
{
    public function showLogin(): void
    {
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);
        include dirname(__DIR__, 2) . '/views/auth/login.php';
    }

    public function login(): void
    {
        $login = trim((string)($_POST['login'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if (!$login || !$password || !Auth::attempt($login, $password)) {
            $_SESSION['flash_error'] = 'Invalid credentials.';
            redirect('/login');
        }

        redirect('/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/login');
    }
}
