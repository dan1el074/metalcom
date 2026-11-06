<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'site'): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = ROOT_PATH . '/app/Views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            throw new \RuntimeException('View não encontrada: ' . $view);
        }
        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();
        require ROOT_PATH . '/app/Views/layouts/' . $layout . '.php';
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function requireCsrf(): void
    {
        $token = (string) ($_POST['_token'] ?? '');
        if (!hash_equals($_SESSION['_token'] ?? '', $token)) {
            http_response_code(419);
            exit('A sessão expirou. Atualize a página e tente novamente.');
        }
    }
}

