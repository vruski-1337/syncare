<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = []): void
    {
        $viewPath = dirname(__DIR__, 2) . '/views/' . $view . '.php';
        if (!is_file($viewPath)) {
            http_response_code(404);
            echo 'View not found';
            return;
        }

        extract($data, EXTR_SKIP);
        $contentView = $viewPath;
        include dirname(__DIR__, 2) . '/views/layout/app.php';
    }
}
