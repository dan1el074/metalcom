<?php

$requestPath = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$path = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, $requestPath);
if (is_file($path)) {
    return false;
}
require __DIR__ . '/index.php';

