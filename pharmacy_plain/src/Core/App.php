<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ModuleController;

final class App
{
    public static function run(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($path === '/') {
            redirect('/login');
        }

        if ($path === '/login' && $method === 'GET') {
            (new AuthController())->showLogin();
            return;
        }

        if ($path === '/login' && $method === 'POST') {
            verify_csrf();
            (new AuthController())->login();
            return;
        }

        if ($path === '/logout' && $method === 'POST') {
            verify_csrf();
            (new AuthController())->logout();
            return;
        }

        if (!Auth::check()) {
            redirect('/login');
        }

        if ($path === '/dashboard') {
            (new DashboardController())->index();
            return;
        }

        if (str_starts_with($path, '/module/')) {
            (new ModuleController())->handle($path, $method);
            return;
        }

        http_response_code(404);
        echo 'Not Found';
    }
}
