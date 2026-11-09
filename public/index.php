<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CatalogController;
use App\Controllers\HomeController;
use App\Controllers\InstallController;

require dirname(__DIR__) . '/app/bootstrap.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$path = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$base = config('base_path') ?: '';
if ($base && str_starts_with($path, $base)) $path = substr($path, strlen($base)) ?: '/';

// Troque para true quando quiser ativar a página de manutenção.
$maintenanceEnabled = false;

if ($maintenanceEnabled) {
    header('Cache-Control: private, no-store, max-age=0');

    $user = \App\Core\Auth::user();
    $authorized = $user && (int) $user['active'] === 1;
    $accessRoute = in_array($path, ['/login', '/logout', '/admin'], true)
        || str_starts_with($path, '/admin/');

    if (!$authorized && !$accessRoute) {
        http_response_code(503);
        header('Retry-After: 3600');
        header('X-Robots-Tag: noindex, nofollow');
        header('Content-Type: text/html; charset=utf-8');
        require ROOT_PATH . '/app/Views/maintenance.php';
        exit;
    }
}

$routes = [
    'GET' => [
        '/' => [HomeController::class, 'index'],
        '/produtos' => [CatalogController::class, 'index'],
        '/produto' => [CatalogController::class, 'show'],
        '/login' => [AuthController::class, 'form'],
        '/install' => [InstallController::class, 'form'],
        '/admin' => [AdminController::class, 'index'],
    ],
    'POST' => [
        '/contato' => [HomeController::class, 'contact'],
        '/login' => [AuthController::class, 'login'],
        '/install' => [InstallController::class, 'create'],
        '/logout' => [AuthController::class, 'logout'],
        '/admin/home' => [AdminController::class, 'saveHome'],
        '/admin/contact' => [AdminController::class, 'saveContact'],
        '/admin/login-appearance' => [AdminController::class, 'saveLoginAppearance'],
        '/admin/users' => [AdminController::class, 'saveUser'],
        '/admin/products' => [AdminController::class, 'saveProduct'],
        '/admin/categories' => [AdminController::class, 'saveCategory'],
    ],
];

if ($method === 'POST' && preg_match('#^/admin/categories/(\d+)/delete$#', $path, $match)) {
    (new AdminController())->deleteCategory((int) $match[1]);
    exit;
}
if ($method === 'POST' && preg_match('#^/admin/users/(\d+)/toggle$#', $path, $match)) {
    (new AdminController())->toggleUser((int) $match[1]);
    exit;
}
if ($method === 'POST' && preg_match('#^/admin/messages/(\d+)/read$#', $path, $match)) {
    (new AdminController())->readMessage((int) $match[1]);
    exit;
}
if ($method === 'POST' && preg_match('#^/admin/products/(\d+)/delete$#', $path, $match)) {
    (new AdminController())->deleteProduct((int) $match[1]);
    exit;
}
if ($method === 'POST' && preg_match('#^/admin/products/(\d+)/toggle$#', $path, $match)) {
    (new AdminController())->toggleProduct((int) $match[1]);
    exit;
}

$action = $routes[$method][$path] ?? null;
if (!$action) {
    http_response_code(404);
    $title = 'Página não encontrada | Metalcom';
    ob_start();
    require ROOT_PATH . '/app/Views/errors/404.php';
    $content = ob_get_clean();
    require ROOT_PATH . '/app/Views/layouts/site.php';
    exit;
}

[$controller, $handler] = $action;
(new $controller())->$handler();
